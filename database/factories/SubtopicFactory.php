<?php
namespace Database\Factories;

use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subtopic>
 */
class SubtopicFactory extends Factory
{
    protected $model = Subtopic::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'topic_id' => Topic::factory()
        ];
    }
}
