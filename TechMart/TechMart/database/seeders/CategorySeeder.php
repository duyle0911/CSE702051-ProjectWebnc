<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Điện thoại',
            'Laptop',
            'Tablet',
            'Phụ kiện',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'category_name' => $categoryName,
            ]);
        }

        $this->command->info('Đã tạo xong các danh mục sản phẩm!');
    }
}
