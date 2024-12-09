<?php
namespace Database\Seeders;
    
use App\Models\Question\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory()
            ->count(50)
            ->hasMatter(5)
            ->hasContent(5)
            ->hasTopic(5)
            ->hasSubTopic(5)
            ->create();
    }


}
