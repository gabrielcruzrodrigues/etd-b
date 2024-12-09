<?php
namespace Database\Factories;

use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserQuestionComment>
 */
class UserQuestionCommentFactory extends Factory
{
    protected $model = UserQuestionComment::class;
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
            'comment' => $this->faker->paragraph,
        ];
    }

    protected static function newFactory()
    {
        return \Database\Factories\UserQuestionCommentFactory::new();
    }
}
