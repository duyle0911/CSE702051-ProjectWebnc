<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

 public function run(): void
    {
        // Tạo tài khoản admin
        User::create([
            'name' => 'Admin TechMart',
            'email' => 'admin@techmart.vn',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '0901234567',
            'address' => 'Số 106, đường Trần Duy Hưng, Trung Hoà, Cầu Giấy ,  Hà Nội',
        ]);

        // Tạo tài khoản khách hàng mẫu
        User::create([
            'name' => 'Nguyễn Văn An',
            'email' => 'nguyenvanan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0912345678',
            'address' => 'Thôn Phú Cường, đường 35, xã Tiền Phong, huyện Mê Linh, Hà Nộ',
        ]);

        User::create([
            'name' => 'Trần Thị Bình',
            'email' => 'tranthibinh@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0923456789',
            'address' => 'Số 210, đường 2 Tháng 9, phường Hòa Cường Bắc, quận Hải Châu, Đà Nẵng',
        ]);

        // Tạo thêm 15 khách hàng ngẫu nhiên
        $vietnameseNames = [
            'Phạm Văn Đức',
            'Hoàng Thị Lan',
            'Vũ Minh Tuấn',
            'Đặng Thị Mai',
            'Bùi Văn Hùng',
            'Ngô Thị Hoa',
            'Dương Minh Khoa',
            'Lý Thị Nga',
            'Trịnh Văn Long',
            'Phan Thị Oanh',
            'Đinh Minh Phúc',
            'Tô Thị Quỳnh',
            'Lưu Văn Sơn',
            'Chu Thị Tâm',
            'Võ Minh Vũ'
        ];

        foreach ($vietnameseNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => 'user' . ($index + 4) . '@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '09' . str_pad($index + 45678901, 8, '0', STR_PAD_LEFT),
                'address' => (100 + $index) . ' Đường ' . ['Hai Bà Trưng', 'Điện Biên Phủ', 'Cách Mạng Tháng 8', 'Nguyễn Thị Minh Khai'][rand(0, 3)] . ', Quận ' . rand(1, 12) . ', TP.HCM',
            ]);
        }

        $this->command->info('Đã tạo xong tài khoản người dùng!');
    }
}
