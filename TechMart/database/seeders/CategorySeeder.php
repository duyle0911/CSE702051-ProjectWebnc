<?php

// Chạy dòng này để lưu dữ liệu 
// php artisan tinker
/* file_put_contents(
    base_path('database/seeders/data/categories.json'),
    json_encode(\App\Models\Category::all()->toArray(), JSON_PRETTY_PRINT)
);*/
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/data/categories.json');

        if (!file_exists($file)) {
            $this->command->error("❌ Không tìm thấy file: categories.json");
            return;
        }

        $data = json_decode(file_get_contents($file), true);

        foreach ($data as $item) {
            unset($item['category_id']); // nếu đang dùng tự tăng ID
            Category::create($item);
        }

        $this->command->info('✅ Đã import danh mục từ file JSON thành công!');
    }
}
