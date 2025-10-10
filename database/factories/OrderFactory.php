<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $russianNames = [
            'Иван Петров', 'Анна Сидорова', 'Дмитрий Козлов', 'Мария Новикова',
            'Александр Волков', 'Елена Соколова', 'Сергей Лебедев', 'Ольга Морозова',
        ];

        $koreanNames = [
            '이반 페트로프', '안나 시도로바', '드미트리 코즐로프', '마리아 노비코바',
            '알렉산드르 볼코프', '엘레나 소콜로바', '세르게이 레베데프', '올가 모로조바',
        ];

        $russianAddresses = [
            'Москва, ул. Тверская, д. 12, кв. 45',
            'Санкт-Петербург, Невский проспект, д. 28, кв. 10',
            'Екатеринбург, ул. Ленина, д. 5, кв. 23',
            'Новосибирск, проспект Карла Маркса, д. 7, кв. 15',
            'Казань, ул. Баумана, д. 32, кв. 8',
        ];

        $koreanAddresses = [
            '모스크바, 트베르스카야 거리, 12번지, 45호',
            '상트페테르부르크, 넵스키 대로, 28번지, 10호',
            '예카테린부르크, 레닌 거리, 5번지, 23호',
            '노보시비르스크, 카를 마르크스 대로, 7번지, 15호',
            '카잔, 바우만 거리, 32번지, 8호',
        ];

        $index = fake()->numberBetween(0, count($russianNames) - 1);
        $customerName = $russianNames[$index];
        $customerNameTranslated = $koreanNames[$index];

        $addressIndex = fake()->numberBetween(0, count($russianAddresses) - 1);
        $customerAddress = $russianAddresses[$addressIndex];
        $customerAddressTranslated = $koreanAddresses[$addressIndex];

        $statuses = ['pending', 'translated', 'wms_sent', 'shipped', 'completed'];

        return [
            'wb_order_id' => 'WB'.fake()->unique()->numberBetween(100000, 999999),
            'customer_name' => $customerName,
            'customer_name_translated' => $customerNameTranslated,
            'customer_address' => $customerAddress,
            'customer_address_translated' => $customerAddressTranslated,
            'customer_phone' => '+7'.fake()->numerify('##########'),
            'status' => fake()->randomElement($statuses),
            'total_amount' => fake()->randomFloat(2, 1000, 50000),
            'currency' => 'RUB',
            'ordered_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'raw_data' => ['source' => 'wildberries', 'original' => true],
        ];
    }
}
