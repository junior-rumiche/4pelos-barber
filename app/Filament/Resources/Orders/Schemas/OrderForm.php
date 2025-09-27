<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'md' => 6,
                ])
                    ->gap(4)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->columnSpan([
                                'md' => 4,
                            ])
                            ->helperText('Selecciona un cliente existente o crea uno nuevo con el botón “+”.')
                            ->createOptionForm([
                                TextInput::make('full_name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(30),
                            ])
                            ->createOptionAction(fn(Action $action) => $action
                                ->label('Nuevo cliente')
                                ->icon('heroicon-o-user-plus')
                                ->modalHeading('Registrar cliente')
                                ->modalSubmitActionLabel('Guardar cliente'))
                            ->createOptionUsing(fn(array $data): int => Customer::create($data)->getKey()),
                        Select::make('status')
                            ->label('Estado')
                            ->options(fn() => collect(OrderResource::getStatusOptions())
                                ->only([Order::STATUS_PENDING, Order::STATUS_IN_PROGRESS])
                                ->all())
                            ->default(Order::STATUS_IN_PROGRESS)
                            ->required()
                            ->native(false)
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->disabled(),
                    ]),
                Repeater::make('items')
                    ->label('Servicios incluidos')
                    ->minItems(1)
                    ->compact()
                    ->collapsible(false)
                    ->addActionAlignment(Alignment::Start)
                    ->table([
                        TableColumn::make('Producto')
                            ->width('60%'),
                        TableColumn::make('Cantidad')
                            ->alignment(Alignment::Center)
                            ->markAsRequired(),
                        TableColumn::make('Precio unitario')
                            ->alignment(Alignment::End)
                            ->markAsRequired(),
                    ])
                    ->afterLabel([
                        Action::make('resetItems')
                            ->label('Reset')
                            ->color('danger')
                            ->button()
                            ->icon('heroicon-o-arrow-path')
                            ->visible(fn(Get $get) => count($get('items') ?? []) > 0)
                            ->action(function (Set $set, Get $get): void {
                                self::resetItems($set, shouldTriggerHooks: false);
                                self::syncTotals($set, $get);
                            }),
                    ])
                    ->afterStateHydrated(fn(Set $set, Get $get) => self::syncTotals($set, $get))
                    ->afterStateUpdated(fn(Set $set, Get $get) => self::syncTotals($set, $get))
                    ->addAction(fn(Action $action) => $action
                        ->label('Add to items')
                        ->color('primary')
                        ->icon('heroicon-o-plus'))
                    ->schema([
                        Select::make('service_id')
                            ->label('Producto')
                            ->hiddenLabel()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->live()
                            ->options(fn() => Service::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->getSearchResultsUsing(fn(string $search): array => Service::query()
                                ->where('name', 'like', "%{$search}%")
                                ->orderBy('name')
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelUsing(fn(?int $value): ?string => $value ? Service::query()->find($value)?->name : null)
                            ->afterStateUpdated(function (Set $set, Get $get, ?int $state, ?int $old, string $operation): void {
                                if (! $state) {
                                    $set('price_at_time_of_order', null, shouldCallUpdatedHooks: true);
                                    self::syncTotals($set, $get);

                                    return;
                                }

                                $service = Service::find($state);
                                $currentPrice = $get('price_at_time_of_order');

                                $hasChangedService = ($old !== null) && ($old !== $state);

                                $shouldResetPrice = ($operation === 'create')
                                    || $hasChangedService
                                    || ($currentPrice === null);

                                if ($shouldResetPrice) {
                                    $set('price_at_time_of_order', $service?->price, shouldCallUpdatedHooks: true);
                                }

                                if (($get('quantity') ?? null) === null) {
                                    $set('quantity', 1, shouldCallUpdatedHooks: true);
                                }

                                self::syncTotals($set, $get);
                            }),
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->hiddenLabel()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->step(1)
                            ->live(onBlur: false)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::syncTotals($set, $get)),
                        TextInput::make('price_at_time_of_order')
                            ->label('Precio unitario (S/)')
                            ->hiddenLabel()
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->live(onBlur: false)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::syncTotals($set, $get)),
                    ]),
            ]);
    }

    protected static function syncTotals(Set $set, Get $get): void
    {
        $items = collect($get('items') ?? []);

        $total = 0.0;

        foreach ($items as $index => $item) {
            $quantity = self::parseQuantity($item['quantity'] ?? null);
            $price = self::parseNumber($item['price_at_time_of_order'] ?? 0.0);

            if (($item['quantity'] ?? null) != $quantity) {
                $set("data.items.{$index}.quantity", $quantity, isAbsolute: true);
            }

            $total += $price * $quantity;
        }

        $set('data.total_amount', $total, isAbsolute: true);
    }

    protected static function resetItems(Set $set, bool $shouldTriggerHooks = true): void
    {
        $set('data.items', [
            [
                'service_id' => null,
                'quantity' => 1,
                'price_at_time_of_order' => null,
            ],
        ], isAbsolute: true, shouldCallUpdatedHooks: $shouldTriggerHooks);

        $set('data.total_amount', 0, isAbsolute: true);
    }

    protected static function parseNumber(mixed $value): float
    {
        if (is_string($value)) {
            $value = str_replace([' ', ','], ['', '.'], $value);
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    protected static function parseQuantity(mixed $value): int
    {
        $quantity = (int) round(self::parseNumber($value ?? 1));

        return max(1, $quantity);
    }
}
