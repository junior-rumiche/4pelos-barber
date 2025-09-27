<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderService extends Pivot
{
    public $incrementing = true;

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    /**
     * The table associated with the model.
     */
    protected $table = 'order_service';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'service_id',
        'price_at_time_of_order',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_at_time_of_order' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    /**
     * Parent order for this item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Service linked to the order.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
