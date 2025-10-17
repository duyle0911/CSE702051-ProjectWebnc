<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // iPhone 15 Pro Max variants
        $iphone15ProMax = Product::where('name', 'iPhone 15 Pro Max')->first();
        if ($iphone15ProMax) {
            ProductVariant::create([
                'product_id' => $iphone15ProMax->product_id,
                'variant_name' => '256GB - Titan Tự Nhiên',
                'additional_price' => 0,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $iphone15ProMax->product_id,
                'variant_name' => '512GB - Titan Xanh',
                'additional_price' => 6000000,
                'stock_quantity' => 12,
            ]);
            ProductVariant::create([
                'product_id' => $iphone15ProMax->product_id,
                'variant_name' => '1TB - Titan Đen',
                'additional_price' => 12000000,
                'stock_quantity' => 8,
            ]);
        }

        // iPhone 15 Pro variants
        $iphone15Pro = Product::where('name', 'iPhone 15 Pro')->first();
        if ($iphone15Pro) {
            ProductVariant::create([
                'product_id' => $iphone15Pro->product_id,
                'variant_name' => '128GB - Titan Tự Nhiên',
                'additional_price' => 0,
                'stock_quantity' => 20,
            ]);
            ProductVariant::create([
                'product_id' => $iphone15Pro->product_id,
                'variant_name' => '256GB - Titan Xanh',
                'additional_price' => 3000000,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $iphone15Pro->product_id,
                'variant_name' => '512GB - Titan Trắng',
                'additional_price' => 6000000,
                'stock_quantity' => 10,
            ]);
        }

        // iPhone 15 variants
        $iphone15 = Product::where('name', 'iPhone 15')->first();
        if ($iphone15) {
            ProductVariant::create([
                'product_id' => $iphone15->product_id,
                'variant_name' => '128GB - Hồng',
                'additional_price' => 0,
                'stock_quantity' => 25,
            ]);
            ProductVariant::create([
                'product_id' => $iphone15->product_id,
                'variant_name' => '256GB - Xanh',
                'additional_price' => 3000000,
                'stock_quantity' => 20,
            ]);
            ProductVariant::create([
                'product_id' => $iphone15->product_id,
                'variant_name' => '512GB - Đen',
                'additional_price' => 6000000,
                'stock_quantity' => 15,
            ]);
        }

        // Samsung Galaxy S24 Ultra variants
        $samsungS24Ultra = Product::where('name', 'Samsung Galaxy S24 Ultra')->first();
        if ($samsungS24Ultra) {
            ProductVariant::create([
                'product_id' => $samsungS24Ultra->product_id,
                'variant_name' => '256GB - Titan Xám',
                'additional_price' => 0,
                'stock_quantity' => 18,
            ]);
            ProductVariant::create([
                'product_id' => $samsungS24Ultra->product_id,
                'variant_name' => '512GB - Titan Tím',
                'additional_price' => 4000000,
                'stock_quantity' => 12,
            ]);
            ProductVariant::create([
                'product_id' => $samsungS24Ultra->product_id,
                'variant_name' => '1TB - Titan Đen',
                'additional_price' => 8000000,
                'stock_quantity' => 8,
            ]);
        }

        // Samsung Galaxy S24+ variants
        $samsungS24Plus = Product::where('name', 'Samsung Galaxy S24+')->first();
        if ($samsungS24Plus) {
            ProductVariant::create([
                'product_id' => $samsungS24Plus->product_id,
                'variant_name' => '256GB - Xám',
                'additional_price' => 0,
                'stock_quantity' => 20,
            ]);
            ProductVariant::create([
                'product_id' => $samsungS24Plus->product_id,
                'variant_name' => '512GB - Tím',
                'additional_price' => 3000000,
                'stock_quantity' => 15,
            ]);
        }

        // MacBook Pro 16 inch M3 variants
        $macbookPro16 = Product::where('name', 'MacBook Pro 16 inch M3')->first();
        if ($macbookPro16) {
            ProductVariant::create([
                'product_id' => $macbookPro16->product_id,
                'variant_name' => 'M3 Pro - 18GB RAM - 512GB SSD - Xám',
                'additional_price' => 0,
                'stock_quantity' => 10,
            ]);
            ProductVariant::create([
                'product_id' => $macbookPro16->product_id,
                'variant_name' => 'M3 Pro - 36GB RAM - 1TB SSD - Bạc',
                'additional_price' => 15000000,
                'stock_quantity' => 8,
            ]);
            ProductVariant::create([
                'product_id' => $macbookPro16->product_id,
                'variant_name' => 'M3 Max - 48GB RAM - 1TB SSD - Xám',
                'additional_price' => 25000000,
                'stock_quantity' => 5,
            ]);
        }

        // MacBook Pro 14 inch M3 variants
        $macbookPro14 = Product::where('name', 'MacBook Pro 14 inch M3')->first();
        if ($macbookPro14) {
            ProductVariant::create([
                'product_id' => $macbookPro14->product_id,
                'variant_name' => 'M3 - 8GB RAM - 512GB SSD - Xám',
                'additional_price' => 0,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $macbookPro14->product_id,
                'variant_name' => 'M3 Pro - 18GB RAM - 1TB SSD - Bạc',
                'additional_price' => 12000000,
                'stock_quantity' => 10,
            ]);
        }

        // MacBook Air 15 inch M2 variants
        $macbookAir15 = Product::where('name', 'MacBook Air 15 inch M2')->first();
        if ($macbookAir15) {
            ProductVariant::create([
                'product_id' => $macbookAir15->product_id,
                'variant_name' => 'M2 - 8GB RAM - 256GB SSD - Bạc',
                'additional_price' => 0,
                'stock_quantity' => 20,
            ]);
            ProductVariant::create([
                'product_id' => $macbookAir15->product_id,
                'variant_name' => 'M2 - 16GB RAM - 512GB SSD - Xám',
                'additional_price' => 8000000,
                'stock_quantity' => 15,
            ]);
        }

        // Dell XPS 13 Plus variants
        $dellXPS13 = Product::where('name', 'Dell XPS 13 Plus')->first();
        if ($dellXPS13) {
            ProductVariant::create([
                'product_id' => $dellXPS13->product_id,
                'variant_name' => 'i5-1340P - 16GB RAM - 512GB SSD',
                'additional_price' => 0,
                'stock_quantity' => 12,
            ]);
            ProductVariant::create([
                'product_id' => $dellXPS13->product_id,
                'variant_name' => 'i7-1360P - 32GB RAM - 1TB SSD',
                'additional_price' => 10000000,
                'stock_quantity' => 8,
            ]);
        }

        // iPad Pro 12.9 inch M2 variants
        $iPadPro129 = Product::where('name', 'iPad Pro 12.9 inch M2')->first();
        if ($iPadPro129) {
            ProductVariant::create([
                'product_id' => $iPadPro129->product_id,
                'variant_name' => '128GB - WiFi - Xám',
                'additional_price' => 0,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $iPadPro129->product_id,
                'variant_name' => '256GB - WiFi - Bạc',
                'additional_price' => 4000000,
                'stock_quantity' => 12,
            ]);
            ProductVariant::create([
                'product_id' => $iPadPro129->product_id,
                'variant_name' => '512GB - WiFi + Cellular - Xám',
                'additional_price' => 10000000,
                'stock_quantity' => 8,
            ]);
        }

        // iPad Pro 11 inch M2 variants
        $iPadPro11 = Product::where('name', 'iPad Pro 11 inch M2')->first();
        if ($iPadPro11) {
            ProductVariant::create([
                'product_id' => $iPadPro11->product_id,
                'variant_name' => '128GB - WiFi - Xám',
                'additional_price' => 0,
                'stock_quantity' => 18,
            ]);
            ProductVariant::create([
                'product_id' => $iPadPro11->product_id,
                'variant_name' => '256GB - WiFi + Cellular - Bạc',
                'additional_price' => 6000000,
                'stock_quantity' => 12,
            ]);
        }

        // Samsung Galaxy Tab S9 Ultra variants
        $tabS9Ultra = Product::where('name', 'Samsung Galaxy Tab S9 Ultra')->first();
        if ($tabS9Ultra) {
            ProductVariant::create([
                'product_id' => $tabS9Ultra->product_id,
                'variant_name' => '256GB - WiFi - Xám',
                'additional_price' => 0,
                'stock_quantity' => 12,
            ]);
            ProductVariant::create([
                'product_id' => $tabS9Ultra->product_id,
                'variant_name' => '512GB - WiFi + 5G - Kem',
                'additional_price' => 5000000,
                'stock_quantity' => 8,
            ]);
        }

        // Samsung Galaxy Tab S9+ variants
        $tabS9Plus = Product::where('name', 'Samsung Galaxy Tab S9+')->first();
        if ($tabS9Plus) {
            ProductVariant::create([
                'product_id' => $tabS9Plus->product_id,
                'variant_name' => '256GB - WiFi - Xám',
                'additional_price' => 0,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $tabS9Plus->product_id,
                'variant_name' => '512GB - WiFi + 5G - Kem',
                'additional_price' => 4000000,
                'stock_quantity' => 10,
            ]);
        }

        // Xiaomi 14 Ultra variants
        $xiaomi14Ultra = Product::where('name', 'Xiaomi 14 Ultra')->first();
        if ($xiaomi14Ultra) {
            ProductVariant::create([
                'product_id' => $xiaomi14Ultra->product_id,
                'variant_name' => '512GB - Đen',
                'additional_price' => 0,
                'stock_quantity' => 10,
            ]);
            ProductVariant::create([
                'product_id' => $xiaomi14Ultra->product_id,
                'variant_name' => '1TB - Trắng',
                'additional_price' => 3000000,
                'stock_quantity' => 8,
            ]);
        }

        // ASUS ZenBook Pro 15 variants
        $asusZenBook = Product::where('name', 'ASUS ZenBook Pro 15')->first();
        if ($asusZenBook) {
            ProductVariant::create([
                'product_id' => $asusZenBook->product_id,
                'variant_name' => 'i7-13700H - 16GB RAM - 512GB SSD - RTX 4060',
                'additional_price' => 0,
                'stock_quantity' => 8,
            ]);
            ProductVariant::create([
                'product_id' => $asusZenBook->product_id,
                'variant_name' => 'i9-13900H - 32GB RAM - 1TB SSD - RTX 4070',
                'additional_price' => 15000000,
                'stock_quantity' => 5,
            ]);
        }

        $this->command->info('Đã tạo xong các biến thể sản phẩm!');
    }
}


/*
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/data/product_variants.json');

        if (!file_exists($file)) {
            $this->command->error('❌ Không tìm thấy file product_variants.json');
            return;
        }

        $data = json_decode(file_get_contents($file), true);

        foreach ($data as $variant) {
            unset($variant['variant_id']); // hoặc 'id' nếu bạn dùng id mặc định
            ProductVariant::create($variant);
        }

        $this->command->info('✅ Đã import biến thể sản phẩm từ JSON!');
    }
}
    */