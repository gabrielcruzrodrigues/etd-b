<?php

use App\Enums\ActiveState;
use App\Models\Content\Content;
use App\Models\Institution\Institution;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Models\Question\Question;
use App\Models\Year\Year;
use App\Services\Question\QuestionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\assertNotNull;

# php artisan test tests/Unit/Services/QuestionServiceTest.php

describe("Question Service", function () {
     beforeEach(function () {
          $this->service = new QuestionService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test('create must save and return a question instance with success', function () {
          $matter = Matter::factory()->create();
          $content = Content::factory()->create();
          $topic = Topic::factory()->create();
          $subtopic = Subtopic::factory()->create();
          $year = Year::factory()->create();
          $institution = Institution::factory()->create();

          // Dados completos para criar uma pergunta sem imagem
          $data = [
               'query' => 'Sample question query',
               'alternative_a' => 'Option A',
               'alternative_b' => 'Option B',
               'alternative_c' => 'Option C',
               'alternative_d' => 'Option D',
               'alternative_e' => 'Option E',
               'answer' => 'A',
               'alternative_has_html' => false,
               'matter_id' => $matter->id,
               'content_id' => $content->id,
               'topic_id' => $topic->id,
               'subtopic_id' => $subtopic->id,
               'difficulty' => 'easy',
               'year_id' => $year->id,
               'institution_id' => $institution->id,
               'state' => ActiveState::ACTIVE->value,
          ];

          // Executa o método create
          $question = $this->service->create($data);

          // Verifica se a pergunta foi salva corretamente no banco de dados
          $this->assertDatabaseHas('questions', [
               'query' => 'Sample question query',
               'answer' => 'A',
               'alternative_has_html' => false,
               'matter_id' => $matter->id,
               'content_id' => $content->id,
               'topic_id' => $topic->id,
               'subtopic_id' => $subtopic->id,
               'difficulty' => 'easy',
               'year_id' => $year->id,
               'institution_id' => $institution->id,
               'state' => ActiveState::ACTIVE->value,
          ]);

          $this->assertInstanceOf(Question::class, $question);
     });

     test('script must save and return a question instance with success', function () {
          Storage::fake('public');

          Http::fake([
               '*' => Http::response('fake_image_content', 200),
          ]);

          $matter = Matter::factory()->create(['name' => 'Física']);
          $content = Content::factory()->create(['name' => 'some-content', 'matter_id' => $matter->id]);
          $topic = Topic::factory()->create(['name' => 'some-topic', 'content_id' => $content->id]);
          $subtopic = Subtopic::factory()->create(['name' => 'some-subtopic', 'topic_id' => $topic->id]);
          $year = Year::factory()->create(['year' => '2022']);
          $institution = Institution::factory()->create(['name' => 'UERJ']);

          $data = [
               [
                    'original_code' => 'Questão 43 143702',
                    'answer' => 'A',
                    'query' => base64_encode('<p>Sample question></p>'),
                    'difficulty' => 'Médio',
                    'matter' => 'Física',
                    'institution' => 'UERJ',
                    'year' => '2022',
                    'content' => 'some-content',
                    'topic' => 'some-topic',
                    'subtopic' => 'some-subtopic',
                    'alternatives' => [
                         [
                              'array_full_perguntas' => [
                                   'Alternative A',
                                   'Alternative B',
                                   'Alternative C',
                                   'Alternative D',
                              ]
                         ]
                    ]
               ]
          ];

          $question = $this->service->createQuestionScript($data);

          $this->assertDatabaseHas('questions', [
               'original_code' => 'Questão 43 143702',
               'answer' => 'A',
               'difficulty' => 'intermediary',
               'matter_id' => $matter->id,
               'content_id' => $content->id,
               'topic_id' => $topic->id,
               'subtopic_id' => $subtopic->id,
               'year_id' => $year->id,
               'institution_id' => $institution->id,
               'alternative_b' => 'Alternative B',
               'alternative_c' => 'Alternative C',
               'alternative_d' => 'Alternative D',
          ]);

          $this->assertNotNull($question->alternative_a);
          $this->assertEquals('Alternative B', $question->alternative_b);
          $this->assertEquals('Alternative C', $question->alternative_c);
          $this->assertEquals('Alternative D', $question->alternative_d);
     });

     test('update must update the question successfuly', function () {
          $matter = Matter::factory()->create();
          $content = Content::factory()->create();
          $topic = Topic::factory()->create();
          $subtopic = Subtopic::factory()->create();
          $year = Year::factory()->create();
          $institution = Institution::factory()->create();

          $question = Question::factory()->create([
               'query' => 'Original query',
               'answer' => 'A',
               'matter_id' => $matter->id,
               'content_id' => $content->id,
               'topic_id' => $topic->id,
               'subtopic_id' => $subtopic->id,
               'year_id' => $year->id,
               'institution_id' => $institution->id,
               'difficulty' => 'easy',
          ]);

          $updateData = [
               'query' => 'Updated query',
               'answer' => 'B',
               'difficulty' => 'intermediary',
          ];

          $this->service->update($updateData, $question->id);

          $this->assertDatabaseHas('questions', [
               'id' => $question->id,
               'query' => 'Updated query',
               'answer' => 'B',
               'difficulty' => 'intermediary',
          ]);

          $updatedQuestion = Question::find($question->id);
          $this->assertEquals('Updated query', $updatedQuestion->query);
          $this->assertEquals('B', $updatedQuestion->answer);
          $this->assertEquals('intermediary', $updatedQuestion->difficulty);
     });

     test('delete must delete a question with success', function () {
          $question = Question::factory()->create();

          $this->service->delete($question->id);

          $this->assertDatabaseMissing('questions', [
               'id' => $question->id,
          ]);
     });

     test('getAll must return a list of question with success', function () {
          $totalQuestions = 10;
          $perPage = 5;
          $page = 1;

          Question::factory()->count($totalQuestions)->create();

          $paginatedQuestions = $this->service->getAll($page, $perPage);

          $this->assertInstanceOf(LengthAwarePaginator::class, $paginatedQuestions);
          $this->assertEquals($totalQuestions, $paginatedQuestions->total());
          $this->assertCount($perPage, $paginatedQuestions->items());
          $this->assertEquals($page, $paginatedQuestions->currentPage());

          $secondPageQuestions = $this->service->getAll(2, $perPage);
          $this->assertCount($totalQuestions - $perPage, $secondPageQuestions->items());
     });

     test('getById must return a Question by id with success', function () {
          $question = Question::factory()->create();

          $foundQuestion = $this->service->getById($question->id);

          $this->assertInstanceOf(Question::class, $foundQuestion);
          $this->assertEquals($question->id, $foundQuestion->id);
     });

     test('getById must return a QuestionInventoryException::questionNotFound() when question not exists', function () {
          $nonExistentId = 999;

          $this->expectException(ModelNotFoundException::class);
          $this->expectExceptionMessage("No query results for model [App\Models\Question\Question] 999");

          $this->service->getById($nonExistentId);
     });

     test('getByCode must return a Question by code with success', function () {
          $question = Question::factory()->create(['code' => 'QST12345']);

          $foundQuestion = $this->service->getByCode('QST12345');

          $this->assertInstanceOf(Question::class, $foundQuestion);
          $this->assertEquals('QST12345', $foundQuestion->code);
     });

     test('getByCode must return a ModelNotFoundException when question not exists', function () {
          $nonExistentCode = 'NONEXISTENT123';

          $this->expectException(ModelNotFoundException::class);
          $this->service->getByCode($nonExistentCode);
     });

     test('query must return a list of question', function () {
          $matchingQuestion = Question::factory()->create([
               'difficulty' => 'easy',
          ]);

          $fields = (object) [
               'matter_id' => null,
               'content_id' => null,
               'topic_id' => null,
               'subtopic_id' => null,
               'year_id' => null,
               'difficulty' => 'easy',
               'institution_id' => null,
               'state' => null,
          ];

          $page = 1;
          $perPage = 15;
          $result = $this->service->query($fields, $page, $perPage);

          $this->assertInstanceOf(LengthAwarePaginator::class, $result);
          $this->assertNotEmpty($result->items(), "Nenhuma questão corresponde aos filtros fornecidos.");
          $this->assertEquals($matchingQuestion->id, $result->items()[0]->id);
     });

     test('getAllFilters must return all filters with success', function () {
          Matter::factory()->count(3)->create();
          Content::factory()->count(3)->create();
          Topic::factory()->count(3)->create();
          Subtopic::factory()->count(3)->create();
          Year::factory()->count(3)->create();
          Institution::factory()->count(3)->create();

          $result = $this->service->getAllFilters();

          $this->assertIsArray($result);
          $this->assertArrayHasKey('matters', $result);
          $this->assertArrayHasKey('contents', $result);
          $this->assertArrayHasKey('topics', $result);
          $this->assertArrayHasKey('subtopics', $result);
          $this->assertArrayHasKey('years', $result);
          $this->assertArrayHasKey('institutions', $result);

          $this->assertInstanceOf(Matter::class, $result['matters']->first());
          $this->assertInstanceOf(Content::class, $result['contents']->first());
          $this->assertInstanceOf(Topic::class, $result['topics']->first());
          $this->assertInstanceOf(Subtopic::class, $result['subtopics']->first());
          $this->assertInstanceOf(Year::class, $result['years']->first());
          $this->assertInstanceOf(Institution::class, $result['institutions']->first());
     });
});
