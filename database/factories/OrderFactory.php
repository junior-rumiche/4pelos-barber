<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement([0, 1, 2]);
        $isPaid = $status === 2;

        return [
            'customer_id' => Customer::factory(),
            'status' => $status,
            'total_amount' => $this->faker->randomFloat(2, 15, 250),
            'created_by_user_id' => User::factory(),
            'payment_processed_by_user_id' => $isPaid ? User::factory() : null,
            'paid_at' => $isPaid ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Order $order): void {
            $services = Service::factory()
                ->count($this->faker->numberBetween(1, 3))
                ->create();

            $total = 0;

            foreach ($services as $service) {
                $quantity = $this->faker->numberBetween(1, 3);

                $order->services()->attach($service->id, [
                    'price_at_time_of_order' => $service->price,
                    'quantity' => $quantity,
                ]);

                $total += (float) $service->price * $quantity;
            }

            $order->update([
                'total_amount' => round($total, 2),
            ]);
        });
    }
}
