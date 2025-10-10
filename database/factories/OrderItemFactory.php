<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $russianProducts = [
            'Смартфон Samsung Galaxy',
            'Набор корейской косметики',
            'Подарочный набор морских водорослей',
            'Очиститель воздуха LG',
            'Корейский женьшень',
            'Набор товаров BTS',
            'Коробка корейской лапши',
            'Маски для лица',
        ];

        $koreanProducts = [
            '삼성 갤럭시 스마트폰',
            'K-뷰티 화장품 세트',
            '한국 김 선물세트',
            'LG 공기청정기',
            '한국 전통 인삼',
            'BTS 굿즈 세트',
            '한국 라면 박스',
            '스킨케어 마스크팩',
        ];

        $index = fake()->numberBetween(0, count($russianProducts) - 1);
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 500, 15000);

        return [
            'product_name' => $russianProducts[$index],
            'product_name_translated' => $koreanProducts[$index],
            'sku' => 'SKU'.fake()->numerify('######'),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $price * $quantity,
        ];
    }
}
