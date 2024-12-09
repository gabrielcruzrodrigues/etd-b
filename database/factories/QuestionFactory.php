<?php

namespace Database\Factories;

use App\Models\Content\Content;
use App\Models\Institution\Institution;
use App\Models\Question\Question;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Models\Year\Year;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * O nome do modelo correspondente a esta fábrica.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'original_code' => $this->faker->unique()->bothify('CODE-####'),
            'code' => $this->faker->unique()->bothify('QST-####'),
            'query' => $this->faker->paragraph,
            'alternative_a' => $this->faker->sentence,
            'alternative_b' => $this->faker->sentence,
            'alternative_c' => $this->faker->sentence,
            'alternative_d' => $this->faker->sentence,
            'alternative_e' => $this->faker->sentence,
            'answer' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'alternative_has_html' => $this->faker->boolean,
            'matter_id' => Matter::factory(), 
            'content_id' => Content::factory(),
            'topic_id' => Topic::factory(),  
            'subtopic_id' => Subtopic::factory(), 
            'difficulty' => $this->faker->randomElement(['easy', 'intermediary', 'hard']),
            'year_id' => Year::factory(), 
            'state' => $this->faker->randomElement(['active', 'disable', 'revision']),
        ];
    }
}
