<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WmsShipment>
 */
class WmsShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
        $carriers = ['CDEK', 'Почта России', 'DHL', 'Boxberry'];

        $requestedAt = fake()->dateTimeBetween('-20 days', '-10 days');
        $shippedAt = fake()->boolean(70) ? fake()->dateTimeBetween($requestedAt, 'now') : null;
        $deliveredAt = $shippedAt && fake()->boolean(50) ? fake()->dateTimeBetween($shippedAt, 'now') : null;

        return [
            'wms_shipment_id' => 'WMS'.fake()->unique()->numerify('########'),
            'tracking_number' => fake()->boolean(80) ? fake()->numerify('##############') : null,
            'status' => fake()->randomElement($statuses),
            'carrier' => fake()->randomElement($carriers),
            'requested_at' => $requestedAt,
            'shipped_at' => $shippedAt,
            'delivered_at' => $deliveredAt,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'wms_response' => [
                'success' => true,
                'message' => 'Shipment processed',
            ],
        ];
    }
}
