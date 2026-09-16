<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FullDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories
        $categories = [
            ['name' => 'Rau củ', 'slug' => 'rau-cu', 'description' => 'Tươi ngon mỗi ngày'],
            ['name' => 'Trái cây', 'slug' => 'trai-cay', 'description' => 'Ngọt lành tự nhiên'],
            ['name' => 'Thịt & trứng', 'slug' => 'thit-trung', 'description' => 'An toàn, chất lượng'],
            ['name' => 'Gạo & hạt', 'slug' => 'gao-hat', 'description' => 'Tinh hoa nông sản Việt'],
            ['name' => 'Đặc sản', 'slug' => 'dac-san', 'description' => 'Hương vị vùng miền'],
        ];

        $categoryModels = [];
        foreach ($categories as $index => $cat) {
            $categoryModels[] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'sort_order' => $index + 1,
                    'status' => 'active',
                ]
            );
        }

        // 2. Seed Products
        $productsData = [
            // Rau củ
            [
                'category_id' => $categoryModels[0]->id,
                'name' => 'Xà lách Đà Lạt',
                'sku' => 'RC-001',
                'price' => 28000,
                'compare_at_price' => 35000,
                'unit' => 'kg',
                'origin' => 'Đà Lạt, Lâm Đồng',
                'featured' => true,
            ],
            [
                'category_id' => $categoryModels[0]->id,
                'name' => 'Cà chua bi',
                'sku' => 'RC-002',
                'price' => 32000,
                'compare_at_price' => 40000,
                'unit' => 'kg',
                'origin' => 'Đà Lạt, Lâm Đồng',
                'featured' => true,
            ],
            [
                'category_id' => $categoryModels[0]->id,
                'name' => 'Cà rốt hữu cơ',
                'sku' => 'RC-003',
                'price' => 25000,
                'compare_at_price' => 30000,
                'unit' => 'kg',
                'origin' => 'Đà Lạt, Lâm Đồng',
                'featured' => false,
            ],
            // Trái cây
            [
                'category_id' => $categoryModels[1]->id,
                'name' => 'Bơ sáp',
                'sku' => 'TC-001',
                'price' => 45000,
                'compare_at_price' => 60000,
                'unit' => 'kg',
                'origin' => 'Bảo Lộc, Lâm Đồng',
                'featured' => true,
            ],
            [
                'category_id' => $categoryModels[1]->id,
                'name' => 'Cam sành Vĩnh Long',
                'sku' => 'TC-002',
                'price' => 35000,
                'compare_at_price' => 45000,
                'unit' => 'kg',
                'origin' => 'Vĩnh Long',
                'featured' => false,
            ],
            [
                'category_id' => $categoryModels[1]->id,
                'name' => 'Dâu tây thủy canh',
                'sku' => 'TC-003',
                'price' => 150000,
                'compare_at_price' => 180000,
                'unit' => 'Hộp 500g',
                'origin' => 'Đà Lạt',
                'featured' => true,
            ],
            // Thịt trứng
            [
                'category_id' => $categoryModels[2]->id,
                'name' => 'Thịt heo thảo mộc',
                'sku' => 'TT-001',
                'price' => 120000,
                'compare_at_price' => 140000,
                'unit' => 'kg',
                'origin' => 'Đồng Nai',
                'featured' => true,
            ],
            [
                'category_id' => $categoryModels[2]->id,
                'name' => 'Trứng gà ta',
                'sku' => 'TT-002',
                'price' => 35000,
                'compare_at_price' => 40000,
                'unit' => 'Chục',
                'origin' => 'Long An',
                'featured' => false,
            ],
            // Đặc sản
            [
                'category_id' => $categoryModels[4]->id,
                'name' => 'Nấm đùi gà',
                'sku' => 'DS-001',
                'price' => 55000,
                'compare_at_price' => 70000,
                'unit' => 'kg',
                'origin' => 'Đà Lạt, Lâm Đồng',
                'featured' => true,
            ],
            [
                'category_id' => $categoryModels[4]->id,
                'name' => 'Mật ong rừng sáp ong',
                'sku' => 'DS-002',
                'price' => 350000,
                'compare_at_price' => 400000,
                'unit' => 'Lọ 500ml',
                'origin' => 'Tây Nguyên',
                'featured' => false,
            ],
        ];

        foreach ($productsData as $pData) {
            $slug = Str::slug($pData['name']);
            
            $product = Product::updateOrCreate(
                ['sku' => $pData['sku']],
                [
                    'category_id' => $pData['category_id'],
                    'name' => $pData['name'],
                    'slug' => $slug,
                    'price' => $pData['price'],
                    'compare_at_price' => $pData['compare_at_price'],
                    'unit' => $pData['unit'],
                    'origin' => $pData['origin'],
                    'short_description' => 'Sản phẩm tươi ngon, chuẩn VietGAP, đảm bảo an toàn.',
                    'description_html' => '<p>Chi tiết sản phẩm đang được cập nhật. Chúng tôi cam kết chất lượng 100%.</p>',
                    'status' => 'active',
                    'featured' => $pData['featured'],
                ]
            );

            // 3. Seed Inventory
            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity_on_hand' => rand(50, 200),
                    'quantity_reserved' => rand(0, 10),
                    'reorder_level' => 20,
                ]
            );

            // 4. Seed Product Images
            // Tạo ảnh placeholder ngẫu nhiên dựa vào tên
            $placeholderColor = match($pData['category_id']) {
                $categoryModels[0]->id => 'eef7eb/0a2d1d', // Rau củ
                $categoryModels[1]->id => 'fff3e0/e65100', // Trái cây
                $categoryModels[2]->id => 'ffebee/b71c1c', // Thịt trứng
                $categoryModels[3]->id => 'fdf8e8/f57f17', // Gạo hạt
                $categoryModels[4]->id => 'f0f4ec/33691e', // Đặc sản
                default => 'f4f6f8/333333'
            };
            
            $encodedName = urlencode($pData['name']);
            $imageUrl = "https://placehold.co/600x600/{$placeholderColor}?text={$encodedName}";
            
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'is_primary' => true,
                ],
                [
                    'file_path' => $imageUrl,
                    'alt_text' => $pData['name'],
                    'sort_order' => 1,
                ]
            );
        }
    }
}
