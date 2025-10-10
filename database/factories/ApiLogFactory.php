<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiLog>
 */
class ApiLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $services = ['wildberries', 'wms', 'translation'];
        $methods = ['GET', 'POST'];
        $statuses = ['success', 'failed', 'error'];

        $service = fake()->randomElement($services);
        $method = fake()->randomElement($methods);
        $status = fake()->randomElement($statuses);

        $endpoints = [
            'wildberries' => ['/api/v3/orders/new', '/api/v3/products', '/api/v3/stock'],
            'wms' => ['/api/shipments', '/api/stock', '/api/tracking'],
            'translation' => ['/translate', '/v2/translate', '/api/translate'],
        ];

        return [
            'service' => $service,
            'method' => $method,
            'endpoint' => fake()->randomElement($endpoints[$service]),
            'request_data' => json_encode(['param1' => 'value1', 'param2' => 'value2']),
            'response_data' => $status === 'success'
                ? json_encode(['success' => true, 'data' => ['result' => 'ok']])
                : json_encode(['error' => 'Something went wrong']),
            'status_code' => $status === 'success' ? 200 : fake()->randomElement([400, 401, 500]),
            'status' => $status,
            'error_message' => $status !== 'success' ? fake()->sentence() : null,
            'duration_ms' => fake()->numberBetween(100, 3000),
        ];
    }
}
