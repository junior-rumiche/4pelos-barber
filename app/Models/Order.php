<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 0;

    public const STATUS_IN_PROGRESS = 1;

    public const STATUS_PAID = 2;

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pendiente de pago',
        self::STATUS_IN_PROGRESS => 'En progreso',
        self::STATUS_PAID => 'Pagado',
    ];

    public const STATUS_COLORS = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_IN_PROGRESS => 'info',
        self::STATUS_PAID => 'success',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'status',
        'total_amount',
        'created_by_user_id',
        'payment_processed_by_user_id',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Status label accessor.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn(): string => self::STATUS_LABELS[$this->status] ?? 'Desconocido');
    }

    /**
     * Determine if the order is marked as paid.
     */
    public function isPaid(): bool
    {
        return (int) $this->status === self::STATUS_PAID;
    }

    public function markAsPaid(?int $processedByUserId = null): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'payment_processed_by_user_id' => $processedByUserId,
        ]);
    }

    public function markAsPending(): void
    {
        if ((int) $this->status === self::STATUS_PENDING) {
            return;
        }

        $this->update([
            'status' => self::STATUS_PENDING,
            'paid_at' => null,
            'payment_processed_by_user_id' => null,
        ]);
    }

    /**
     * Customer associated with the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * User who created the order.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * User who processed the payment.
     */
    public function paymentProcessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_processed_by_user_id');
    }

    /**
     * Services associated with the order including pivot details.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'order_service')
            ->using(OrderService::class)
            ->withPivot(['price_at_time_of_order', 'quantity']);
    }

    /**
     * Pivot items for this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderService::class)->with('service');
    }
}
