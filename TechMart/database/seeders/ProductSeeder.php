<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        
        // Lấy tất cả categories
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('Không có danh mục nào. Vui lòng chạy CategorySeeder trước.');
            return;
        }

        // Dữ liệu sản phẩm thực tế từ thị trường Việt Nam
        $productTemplates = [
            'Điện thoại' => [
                // iPhone Series
                ['name' => 'iPhone 15 Pro Max', 'price' => 29990000],
                ['name' => 'iPhone 15 Pro', 'price' => 24990000],
                ['name' => 'iPhone 15 Plus', 'price' => 22990000],
                ['name' => 'iPhone 15', 'price' => 19990000],
                ['name' => 'iPhone 14 Pro Max', 'price' => 26990000],
                ['name' => 'iPhone 14 Pro', 'price' => 22990000],
                ['name' => 'iPhone 14', 'price' => 17990000],
                ['name' => 'iPhone 13', 'price' => 14990000],
                
                // Samsung Galaxy Series
                ['name' => 'Samsung Galaxy S24 Ultra', 'price' => 29990000],
                ['name' => 'Samsung Galaxy S24+', 'price' => 24990000],
                ['name' => 'Samsung Galaxy S24', 'price' => 19990000],
                ['name' => 'Samsung Galaxy S23 FE', 'price' => 14990000],
                ['name' => 'Samsung Galaxy A55', 'price' => 9990000],
                ['name' => 'Samsung Galaxy A35', 'price' => 7990000],
                ['name' => 'Samsung Galaxy A25', 'price' => 5990000],
                ['name' => 'Samsung Galaxy A15', 'price' => 4490000],
                
                // Xiaomi Series
                ['name' => 'Xiaomi 14 Ultra', 'price' => 24990000],
                ['name' => 'Xiaomi 14', 'price' => 17990000],
                ['name' => 'Xiaomi Redmi Note 13 Pro', 'price' => 6990000],
                ['name' => 'Xiaomi Redmi Note 13', 'price' => 4990000],
            ],
            'Laptop' => [
                // MacBook Series
                ['name' => 'MacBook Pro 16 inch M3', 'price' => 59990000],
                ['name' => 'MacBook Pro 14 inch M3', 'price' => 44990000],
                ['name' => 'MacBook Air 15 inch M2', 'price' => 32990000],
                ['name' => 'MacBook Air 13 inch M2', 'price' => 27990000],
                ['name' => 'MacBook Air 13 inch M1', 'price' => 22990000],
                
                // Dell Series
                ['name' => 'Dell XPS 13 Plus', 'price' => 39990000],
                ['name' => 'Dell XPS 15', 'price' => 49990000],
                ['name' => 'Dell Inspiron 15 3000', 'price' => 12990000],
                ['name' => 'Dell Vostro 15 3000', 'price' => 14990000],
                
                // HP Series
                ['name' => 'HP Spectre x360', 'price' => 42990000],
                ['name' => 'HP Envy x360', 'price' => 24990000],
                ['name' => 'HP Pavilion 15', 'price' => 16990000],
                ['name' => 'HP EliteBook 840', 'price' => 35990000],
                
                // ASUS Series
                ['name' => 'ASUS ZenBook Pro 15', 'price' => 45990000],
                ['name' => 'ASUS ZenBook 14', 'price' => 19990000],
                ['name' => 'ASUS VivoBook S15', 'price' => 14990000],
                ['name' => 'ASUS ROG Strix G15', 'price' => 29990000],
                
                // Lenovo Series
                ['name' => 'Lenovo ThinkPad X1 Carbon', 'price' => 39990000],
                ['name' => 'Lenovo ThinkPad E14', 'price' => 17990000],
                ['name' => 'Lenovo IdeaPad 3', 'price' => 12990000],
                ['name' => 'Lenovo Legion 5', 'price' => 24990000],
                
                // Acer Series
                ['name' => 'Acer Swift 3', 'price' => 16990000],
                ['name' => 'Acer Aspire 5', 'price' => 13990000],
            ],
            'Tablet' => [
                // iPad Series
                ['name' => 'iPad Pro 12.9 inch M2', 'price' => 26990000],
                ['name' => 'iPad Pro 11 inch M2', 'price' => 20990000],
                ['name' => 'iPad Air 5', 'price' => 14990000],
                ['name' => 'iPad 10.9 inch', 'price' => 10990000],
                ['name' => 'iPad mini 6', 'price' => 12990000],
                
                // Samsung Galaxy Tab Series
                ['name' => 'Samsung Galaxy Tab S9 Ultra', 'price' => 24990000],
                ['name' => 'Samsung Galaxy Tab S9+', 'price' => 19990000],
                ['name' => 'Samsung Galaxy Tab S9', 'price' => 15990000],
                ['name' => 'Samsung Galaxy Tab S9 FE+', 'price' => 11990000],
                ['name' => 'Samsung Galaxy Tab S9 FE', 'price' => 8990000],
                ['name' => 'Samsung Galaxy Tab A9+', 'price' => 5990000],
                ['name' => 'Samsung Galaxy Tab A9', 'price' => 3990000],
                
                // Xiaomi Pad Series
                ['name' => 'Xiaomi Pad 6', 'price' => 7990000],
                ['name' => 'Xiaomi Pad 5', 'price' => 6990000],
                ['name' => 'Xiaomi Redmi Pad SE', 'price' => 3990000],
                
                // Lenovo Tab Series
                ['name' => 'Lenovo Tab P11 Plus', 'price' => 6990000],
                ['name' => 'Lenovo Tab M10', 'price' => 3490000],
                
                // Surface Series
                ['name' => 'Microsoft Surface Pro 9', 'price' => 24990000],
                ['name' => 'Microsoft Surface Go 3', 'price' => 12990000],
            ],
            'Phụ kiện' => [
                // Tai nghe không dây
                ['name' => 'AirPods Pro 2', 'price' => 6490000],
                ['name' => 'AirPods 3', 'price' => 4490000],
                ['name' => 'AirPods 2', 'price' => 2990000],
                ['name' => 'Sony WH-1000XM5', 'price' => 7990000],
                ['name' => 'Sony WH-1000XM4', 'price' => 5990000],
                ['name' => 'Bose QuietComfort 45', 'price' => 6990000],
                ['name' => 'JBL Live 660NC', 'price' => 2490000],
                
                // Sạc và cáp
                ['name' => 'Sạc nhanh Apple 20W', 'price' => 590000],
                ['name' => 'Sạc nhanh Anker 67W', 'price' => 1290000],
                ['name' => 'Sạc không dây MagSafe', 'price' => 1190000],
                ['name' => 'Cáp Lightning 1m', 'price' => 490000],
                ['name' => 'Cáp USB-C 2m', 'price' => 390000],
                ['name' => 'Hub USB-C 7 in 1', 'price' => 890000],
                
                // Ốp lưng và bảo vệ
                ['name' => 'Ốp lưng iPhone 15 Pro Max', 'price' => 290000],
                ['name' => 'Ốp lưng Samsung S24 Ultra', 'price' => 250000],
                ['name' => 'Miếng dán cường lực iPhone', 'price' => 190000],
                ['name' => 'Miếng dán PPF MacBook', 'price' => 590000],
                
                // Phụ kiện khác
                ['name' => 'Apple Pencil 2', 'price' => 3290000],
                ['name' => 'Magic Keyboard iPad', 'price' => 7990000],
                ['name' => 'Chuột Magic Mouse', 'price' => 2290000],
                ['name' => 'Bàn phím Magic Keyboard', 'price' => 2990000],
                ['name' => 'Giá đỡ laptop nhôm', 'price' => 490000],
                ['name' => 'Pin dự phòng 20000mAh', 'price' => 590000],
            ]
        ];

        foreach ($categories as $category) {
            $categoryName = $category->category_name;
            $templates = $productTemplates[$categoryName] ?? [];

            foreach ($templates as $template) {
                Product::create([
                    'name' => $template['name'],
                    'description' => $this->generateDescription($template['name'], $categoryName, $faker),
                    'price' => $template['price'],
                    'stock_quantity' => $faker->numberBetween(10, 100),
                    'category_id' => $category->category_id,
                    'image_url' => null,
                ]);
            }
            
            $this->command->info("Đã tạo " . count($templates) . " sản phẩm cho danh mục: {$categoryName}");
        }
        
        $this->command->info('Đã tạo xong tất cả sản phẩm!');
    }

    private function generateDescription($productName, $categoryName, $faker)
    {
        $descriptions = [
            'Điện thoại' => [
                'Điện thoại thông minh cao cấp với thiết kế sang trọng, hiệu năng mạnh mẽ và camera chất lượng cao.',
                'Smartphone flagship với công nghệ tiên tiến, màn hình sắc nét và pin bền bỉ cả ngày.',
                'Thiết kế premium, xử lý mượt mà mọi tác vụ từ cơ bản đến chuyên nghiệp.',
            ],
            'Laptop' => [
                'Laptop cao cấp với hiệu năng mạnh mẽ, phù hợp cho công việc và sáng tạo chuyên nghiệp.',
                'Máy tính xách tay thiết kế mỏng nhẹ, pin lâu và màn hình sắc nét.',
                'Laptop đa năng với cấu hình mạnh, bàn phím thoải mái và kết nối đa dạng.',
            ],
            'Tablet' => [
                'Máy tính bảng cao cấp với màn hình lớn, hiệu năng mạnh mẽ cho học tập và làm việc.',
                'Tablet đa năng với thiết kế mỏng nhẹ, pin bền và trải nghiệm giải trí tuyệt vời.',
                'Máy tính bảng premium hỗ trợ bút cảm ứng và bàn phím rời cho năng suất cao.',
            ],
            'Phụ kiện' => [
                'Phụ kiện chính hãng với chất lượng cao, thiết kế tinh tế và độ bền vượt trội.',
                'Accessory premium tương thích hoàn hảo với các thiết bị Apple và Android.',
                'Phụ kiện thông minh giúp nâng cao trải nghiệm sử dụng thiết bị của bạn.',
            ]
        ];

        $categoryDescriptions = $descriptions[$categoryName] ?? $descriptions['Phụ kiện'];
        $baseDescription = $faker->randomElement($categoryDescriptions);
        
        $details = [
            "✅ Bảo hành chính hãng 12 tháng",
            "✅ Giao hàng miễn phí toàn quốc",
            "✅ Hỗ trợ trả góp 0% lãi suất",
            "✅ Đổi trả trong 15 ngày",
            "✅ Tư vấn kỹ thuật 24/7"
        ];
        
        return $baseDescription . "\n\n" . implode("\n", $faker->randomElements($details, 3));
    }
}
