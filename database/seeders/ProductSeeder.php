<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan telur, ayam, dan sosis.',
            'price' => 25000.00,
        ]);

        Product::create([
            'name' => 'Mie Ayam Bakso',
            'description' => 'Mie ayam lengkap dengan bakso sapi.',
            'price' => 20000.00,
        ]);

        Product::create([
            'name' => 'Es Teh Manis',
            'description' => 'Teh manis dingin yang menyegarkan.',
            'price' => 5000.00,
        ]);
    }
}