<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Hair Cut',
                'price' => 25.00,
                'description' => 'Professional haircut with styling and finishing',
                'is_active' => true,
            ],
            [
                'name' => 'Beard Trim',
                'price' => 15.00,
                'description' => 'Precise beard trimming and shaping',
                'is_active' => true,
            ],
            [
                'name' => 'Shave',
                'price' => 20.00,
                'description' => 'Traditional straight razor shave with hot towel',
                'is_active' => true,
            ],
            [
                'name' => 'Hair Wash',
                'price' => 10.00,
                'description' => 'Relaxing hair wash and conditioning treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Hair Styling',
                'price' => 30.00,
                'description' => 'Professional hair styling for special occasions',
                'is_active' => true,
            ],
            [
                'name' => 'Eyebrow Trim',
                'price' => 8.00,
                'description' => 'Eyebrow shaping and trimming',
                'is_active' => true,
            ],
            [
                'name' => 'Hair Coloring',
                'price' => 80.00,
                'description' => 'Professional hair coloring service',
                'is_active' => true,
            ],
            [
                'name' => 'Hair Treatment',
                'price' => 45.00,
                'description' => 'Deep conditioning and hair repair treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Scalp Massage',
                'price' => 25.00,
                'description' => 'Relaxing scalp massage therapy',
                'is_active' => true,
            ],
            [
                'name' => 'Kids Hair Cut',
                'price' => 18.00,
                'description' => 'Gentle haircut for children under 12',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
