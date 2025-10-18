<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Category::pluck('id','name');
        $rows = [
            ['Smartphone', 699.99, 'Latest gen phone', 'Electronics', 50, 'Active'],
            ['Notebook', 4.99, 'A5 lined notebook', 'Books', 200, 'Active'],
            ['T-Shirt', 12.50, 'Cotton tee', 'Clothing', 120, 'Inactive'],
            ['Blender', 89.00, 'Kitchen blender', 'Home', 30, 'Active'],
            ['Puzzle', 14.90, '1000-piece puzzle', 'Toys', 75, 'Active'],
        ];
        foreach ($rows as [$name,$price,$desc,$catName,$stock,$status]) {
            Product::updateOrCreate(
                ['name' => $name],
                [
                    'price' => $price,
                    'description' => $desc,
                    'category_id' => $cats[$catName] ?? Category::first()->id,
                    'stock' => $stock,
                    'status' => $status
                ]
            );
        }
    }
}