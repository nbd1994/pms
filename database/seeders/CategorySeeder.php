<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Electronics','Books','Clothing','Home','Toys'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}