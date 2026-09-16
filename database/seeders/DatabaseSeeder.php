<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@nongsanxanh.com'],
            [
                'name' => 'Chủ Hệ Thống',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'phone' => '0999999999',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@nongsanxanh.com'],
            [
                'name' => 'Tên User',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'phone' => '0888888888',
                'role' => 'customer',
                'status' => 'active',
            ]
        );

        // Thêm 2 dòng này — dữ liệu catalog cho việc test luồng mua hàng
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}