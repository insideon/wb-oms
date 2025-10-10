<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            ['name' => '삼성 갤럭시 스마트폰', 'category' => '전자제품'],
            ['name' => 'K-뷰티 화장품 세트', 'category' => '뷰티'],
            ['name' => '한국 김 선물세트', 'category' => '식품'],
            ['name' => 'LG 공기청정기', 'category' => '가전'],
            ['name' => '한국 전통 인삼', 'category' => '건강'],
            ['name' => 'BTS 굿즈 세트', 'category' => '엔터테인먼트'],
            ['name' => '한국 라면 박스', 'category' => '식품'],
            ['name' => '스킨케어 마스크팩', 'category' => '뷰티'],
        ];

        $product = fake()->randomElement($products);

        return [
            'wb_product_id' => 'WBPROD'.fake()->unique()->numberBetween(10000, 99999),
            'name' => $product['name'],
            'sku' => 'SKU'.fake()->unique()->numerify('######'),
            'description' => fake()->sentence(10),
            'stock_quantity' => fake()->numberBetween(0, 500),
            'price' => fake()->randomFloat(2, 500, 30000),
            'category' => $product['category'],
            'barcode' => fake()->ean13(),
            'images' => [
                'https://via.placeholder.com/400x400',
                'https://via.placeholder.com/400x400',
            ],
            'is_active' => fake()->boolean(90),
            'last_synced_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
