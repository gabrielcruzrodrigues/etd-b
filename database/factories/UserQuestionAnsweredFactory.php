<?php
namespace Database\Factories;

use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnswered;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserQuestionAnswered>
 */
class UserQuestionAnsweredFactory extends Factory
{
    protected $model = UserQuestionAnswered::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'question_id' => Question::factory(),
            'alternative' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'error_notebook' => $this->faker->optional()->randomElement(['certainty', 'content', 'interpretation', 'distraction', 'kicked']),
        ];
    }
}
