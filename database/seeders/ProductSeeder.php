<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        Product::factory(20)->create()->each(function ($products) use ($categories) {
            $products->categories()->attach(
                $categories->random(rand(1, 5))->pluck('id')->toArray()
            );
        });
    }
}
