<?php
namespace Database\Factories;

use App\Models\Year\Year;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Year\Year>
 */
class YearFactory extends Factory
{
    protected $model = Year::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => $this->faker->unique()->year
        ];
    }
}
