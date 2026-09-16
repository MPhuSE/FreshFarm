<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();

        if (! $category) {
            $this->command->warn('Chưa có category nào, chạy CategorySeeder trước.');
            return;
        }

        $products = [
            ['name' => 'Cam Vinh',            'unit' => 'kg', 'price' => 45000, 'qty' => 38],
            ['name' => 'Bắp cải Đà Lạt',       'unit' => 'kg', 'price' => 20000, 'qty' => 50],
            ['name' => 'Gạo ST25',             'unit' => 'kg', 'price' => 32000, 'qty' => 100],
            ['name' => 'Xoài cát Hòa Lộc',     'unit' => 'kg', 'price' => 60000, 'qty' => 25],
            ['name' => 'Khoai lang mật',       'unit' => 'kg', 'price' => 18000, 'qty' => 0], // test case hết hàng
        ];

        foreach ($products as $item) {
            $sku = strtoupper(Str::slug($item['name'], '-'));

            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'category_id' => $category->id,
                    'name'        => $item['name'],
                    'slug'        => Str::slug($item['name']),
                    'unit'        => $item['unit'],
                    'price'       => $item['price'],
                    'status'      => 'active',
                ]
            );

            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity_on_hand'  => $item['qty'],
                    'quantity_reserved' => 0,
                    'reorder_level'     => 5,
                ]
            );
        }
    }
}