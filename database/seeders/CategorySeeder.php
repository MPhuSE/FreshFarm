<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Rau củ', 'Trái cây', 'Gạo & Hạt', 'Đặc sản vùng miền', 'Thực phẩm khô'];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'status' => 'active',
                ]
            );
        }
    }
}