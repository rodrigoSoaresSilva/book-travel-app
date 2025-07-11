<?php

namespace Database\Factories;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = TravelRequest::class;

    public function definition(): array
    {
        $departure = $this->faker->dateTimeBetween('now', '+1 month');
        $return = (clone $departure)->modify('+'.rand(1, 10).' days');

        return [
            'user_id' => User::factory(),
            'destination' => $this->faker->city(),
            'departure_date' => $departure->format('Y-m-d'),
            'return_date' => $return->format('Y-m-d'),
            'status' => $this->faker->randomElement(['S', 'A', 'C']),
            'approved_by' => null,
            'canceled_by' => null,
        ];
    }

    // Estado específico para status
    public function solicitado(): static
    {
        return $this->state(['status' => 'S']);
    }

    public function aprovado(int $adminId): static
    {
        return $this->state([
            'status' => 'A',
            'approved_by' => $adminId,
        ]);
    }

    public function cancelado(int $adminId): static
    {
        return $this->state([
            'status' => 'C',
            'canceled_by' => $adminId,
        ]);
    }

}
