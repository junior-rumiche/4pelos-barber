<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $items = $this->record
            ->items()
            ->with('service')
            ->get()
            ->map(fn($item): array => [
                'id' => $item->id,
                'service_id' => $item->service_id,
                'price_at_time_of_order' => $item->price_at_time_of_order,
                'quantity' => $item->quantity,
            ])
            ->toArray();

        $items = $this->normalizeItems($items);
        $total = $this->sumItems($items);

        $data['items'] = $items;
        $data['total_amount'] = $total;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->prepareOrderData($data);
        $data['created_by_user_id'] = $this->record->created_by_user_id;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        return DB::transaction(function () use ($record, $data, $items): Model {
            $record->update($data);

            $record->items()->delete();
            $record->items()->createMany($this->preparePivotItems($items));

            return $record;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareOrderData(array $data): array
    {
        $data['items'] = $this->normalizeItems($data['items'] ?? []);
        $data['total_amount'] = $this->sumItems($data['items']);

        $status = (int) ($data['status'] ?? Order::STATUS_PENDING);

        if ($status === Order::STATUS_PAID) {
            $status = $this->record->status;
        }

        $data['status'] = $status;

        if ($status === Order::STATUS_PAID) {
            $data['paid_at'] = $this->record->paid_at;
            $data['payment_processed_by_user_id'] = $this->record->payment_processed_by_user_id;
        } else {
            $data['paid_at'] = null;
            $data['payment_processed_by_user_id'] = null;
        }

        return $data;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function sumItems(array $items): float
    {
        return collect($items)
            ->sum(function (array $item): float {
                $price = round($this->parseNumber($item['price_at_time_of_order'] ?? 0.0), 2);
                $quantity = $this->parseQuantity($item['quantity'] ?? 1);

                return $price * $quantity;
            });
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function normalizeItems(array $items): array
    {
        return collect($items)
            ->filter(fn($item): bool => filled(($item['service_id'] ?? null)))
            ->values()
            ->map(function ($item): array {
                $price = $this->parseNumber($item['price_at_time_of_order'] ?? 0.0);
                $quantity = $this->parseQuantity($item['quantity'] ?? 1);

                return [
                    ...$item,
                    'price_at_time_of_order' => $price,
                    'quantity' => $quantity,
                ];
            })
            ->toArray();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function preparePivotItems(array $items): array
    {
        return collect($items)
            ->map(fn($item): array => Arr::only((array) $item, ['service_id', 'price_at_time_of_order', 'quantity']))
            ->groupBy(fn(array $item) => (int) $item['service_id'])
            ->map(function ($group, int $serviceId): array {
                $quantity = $group->sum(fn(array $item): int => (int) $item['quantity']);
                $lastItem = $group->last();

                return [
                    'service_id' => $serviceId,
                    'quantity' => max(1, $quantity),
                    'price_at_time_of_order' => round((float) ($lastItem['price_at_time_of_order'] ?? 0), 2),
                ];
            })
            ->values()
            ->all();
    }

    private function parseNumber(mixed $value): float
    {
        if (is_string($value)) {
            $value = str_replace([' ', ','], ['', '.'], $value);
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function parseQuantity(mixed $value): int
    {
        $quantity = $this->parseNumber($value ?? 1);

        return max(1, (int) round($quantity));
    }
}
