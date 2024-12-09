<?php
namespace Database\Factories;

use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnnotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\UserQuestionAnnotation>
 */
class UserQuestionAnnotationFactory extends Factory
{
    protected $model = UserQuestionAnnotation::class;
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
            'annotation' => $this->faker->paragraph,
        ];
    }
}
