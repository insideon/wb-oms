<?php

namespace Database\Seeders;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WmsShipment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 데이터 시딩 시작...');

        // 관리자 사용자 생성
        if (! User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);
            $this->command->info('✅ 관리자 계정 생성 완료');
        }

        // 상품 생성 (20개)
        $this->command->info('📦 상품 생성 중...');
        $products = Product::factory(20)->create();
        $this->command->info('✅ 상품 20개 생성 완료');

        // 주문 생성 (50개)
        $this->command->info('🛒 주문 생성 중...');
        Order::factory(50)->create()->each(function ($order) use ($products) {
            // 각 주문에 1-4개의 아이템 추가
            $itemCount = rand(1, 4);
            $totalAmount = 0;

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => 'Товар: '.$product->name,
                    'product_name_translated' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // 주문 총액 업데이트
            $order->update(['total_amount' => $totalAmount]);

            // 일부 주문에 배송 정보 추가 (60% 확률)
            if (in_array($order->status, ['wms_sent', 'shipped', 'completed']) && rand(1, 100) <= 60) {
                WmsShipment::factory()->create([
                    'order_id' => $order->id,
                ]);
            }
        });
        $this->command->info('✅ 주문 50개 및 주문 아이템 생성 완료');

        // API 로그 생성 (100개)
        $this->command->info('📝 API 로그 생성 중...');
        ApiLog::factory(100)->create();
        $this->command->info('✅ API 로그 100개 생성 완료');

        $this->command->info('');
        $this->command->info('🎉 모든 데이터 시딩 완료!');
        $this->command->info('');
        $this->command->info('📊 생성된 데이터:');
        $this->command->info('   - 상품: '.Product::count().'개');
        $this->command->info('   - 주문: '.Order::count().'개');
        $this->command->info('   - 배송: '.WmsShipment::count().'개');
        $this->command->info('   - API 로그: '.ApiLog::count().'개');
    }
}
