<?php

namespace Database\Seeders;

use App\Models\Content\Content;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnnotation;
use App\Models\User\UserQuestionAnswered;
use App\Models\User\UserQuestionComment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $matters = Matter::factory()->count(3)->create();
        $contents = Content::factory()->count(3)->create();
        $topics = Topic::factory()->count(3)->create();
        $subtopics = Subtopic::factory()->count(3)->create();

        User::factory()->count(10)->create();

        Question::factory()
            ->count(5)
            ->create()
            ->each(function ($question) use ($matters, $contents, $topics, $subtopics) {
                $question->matter_id = $matters->random()->id;
                $question->content_id = $contents->random()->id;
                $question->topic_id = $topics->random()->id;
                $question->subtopic_id = $subtopics->random()->id;
                $question->save();
            });

        UserQuestionAnswered::factory()->count(3)->create();
        UserQuestionAnnotation::factory()->count(3)->create();
        UserQuestionComment::factory()->count(3)->create();
    }
}
