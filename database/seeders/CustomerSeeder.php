<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'full_name' => 'Carlos Ramírez',
                'phone' => '+51 987 654 321',
            ],
            [
                'full_name' => 'María González',
                'phone' => '+51 956 123 456',
            ],
            [
                'full_name' => 'Luis Fernández',
                'phone' => null,
            ],
            [
                'full_name' => 'Ana Torres',
                'phone' => '+51 965 478 123',
            ],
            [
                'full_name' => 'Jorge Castillo',
                'phone' => null,
            ],
            [
                'full_name' => 'Lucía Maldonado',
                'phone' => '+51 914 785 632',
            ],
            [
                'full_name' => 'Ricardo Pineda',
                'phone' => '+51 998 321 654',
            ],
            [
                'full_name' => 'Fernanda Valdez',
                'phone' => null,
            ],
            [
                'full_name' => 'Sofía Andrade',
                'phone' => '+51 911 223 344',
            ],
            [
                'full_name' => 'Héctor Rojas',
                'phone' => '+51 933 445 667',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
