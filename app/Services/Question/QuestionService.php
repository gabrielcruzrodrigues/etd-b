<?php

namespace App\Services\Question;

use App\Contracts\QuestionServiceContract;
use App\Enums\ActiveState;
use App\Enums\Difficulty;
use App\Exceptions\CustomException;
use App\Exceptions\Matter\ContentInventoryException;
use App\Exceptions\Matter\MatterServiceExceptions;
use App\Exceptions\Matter\SubtopicInventoryException;
use App\Exceptions\Matter\TopicInventoryException;
use App\Exceptions\QuestionInventoryException;
use App\Models\Content\Content;
use App\Models\Institution\Institution;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Models\Question\Question;
use App\Models\Year\Year;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use \Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class QuestionService implements QuestionServiceContract
{
     private string $aws_images_question_folder;
     private string $aws_url_path;

     public function __construct()
     {
          $this->aws_images_question_folder = config('app.aws_images_question_folder');
          $this->aws_url_path = config('app.aws_url_path');
     }

     /**
      * @throws QuestionInventoryException
      */
     public function create(array $data): Question
     {
          $this->verifyModelsBeforeCreateQuestion($data);
          $code = $this->generateCode();
          $imageName = null;

          if (isset($data['imageFile'])) {

               if (!$data['imageFile'] instanceof UploadedFile) {
                    throw QuestionInventoryException::isNotFile();
               }

               $imageName = $this->saveQuestionImage($data['imageFile'], $code);
          }

          $arrayWithQuestionForSave = array_merge($data, [
               'code' => $code,
               'image' => $imageName ?? null,
               'state' => ActiveState::ACTIVE->value,
          ]);

          unset($arrayWithQuestionForSave['imageFile']);

          return Question::create($arrayWithQuestionForSave);
     }

     protected function verifyModelsBeforeCreateQuestion(array $data)
     {
          if ($data['matter_id'] !== null) {
               $matter = Matter::find($data['matter_id']);

               if ($matter == null) {
                    throw MatterServiceExceptions::matterNotFound();
               }
          }

          if ($data['content_id'] !== null) {
               $content = Content::find($data['content_id']);

               if ($content == null) {
                    throw ContentInventoryException::contentNotFound();
               }
          }

          if ($data['topic_id'] !== null) {
               $topic = Topic::find($data['topic_id']);

               if ($topic == null) {
                    throw TopicInventoryException::topicNotFound();
               }
          }

          if ($data['subtopic_id'] !== null) {
               $subtopic = Subtopic::find($data['subtopic_id']);

               if ($subtopic == null) {
                    throw SubtopicInventoryException::subtopicNotFound();
               }
          }
     }

     public function createQuestionScript(array $request)
     {
          $data = $request[0];
          $code = $this->generateCode();

          //remove unnecessary informations in original_code
          $data['original_code'] = preg_replace('/Fácil\s\d{2}:\d{2}/', '', $data['original_code']);
          $data['original_code'] = preg_replace('/Médio\s\d{2}:\d{2}/', '', $data['original_code']);
          $data['original_code'] = preg_replace('/Difícil\s\d{2}:\d{2}/', '', $data['original_code']);
          $data['original_code'] = trim($data['original_code']);

          if (!isset($data['query'])) {
               throw QuestionInventoryException::scriptException("Query field not found!");
          }

          $queryHtml = base64_decode($data['query']);
          $alternatives = $data['alternatives'][0]['array_full_perguntas'];

          preg_match_all('/src="([^"]+)"/', $queryHtml, $matches);

          //Here we save the original path and the new filename in a dictionary to replace after
          $QuerySrcDictionary = [];
          foreach ($matches[0] as $index => $fullMatch) {
               $cleanUrl = htmlspecialchars_decode($matches[1][$index]);
               $QuerySrcDictionary["occurrence_{$index}"] = [
                    'original' => $fullMatch,
                    'fileName' => $this->downloadAndSaveImagesReturningFileName($cleanUrl, $code)
               ];
          }

          foreach ($QuerySrcDictionary as $occurrence) {
               $queryHtml = str_replace($occurrence['original'], 'src="' . $occurrence['fileName'] . '"', $queryHtml);
          }

          //Here we save the original path and the new filename in a dictionary to replace after
          $alternativesSrcDictionary = [];
          foreach ($alternatives as $altIndex => $alternative) {
               if (preg_match('/src="([^"]+)"/', $alternative)) {
                    preg_match_all('/src="([^"]+)"/', $alternative, $altMatches);

                    foreach ($altMatches[0] as $index => $fullcontent) {
                         $cleanUrl = htmlspecialchars_decode($altMatches[1][$index]);
                         $alternativesSrcDictionary["occurrence_{$index}"] = [
                              'original' => $fullcontent,
                              'fileName' => $this->downloadAndSaveImagesReturningFileName($cleanUrl, $code)
                         ];
                    }

                    foreach ($alternativesSrcDictionary as $occurrence) {
                         $alternative = str_replace($occurrence['original'], 'src="' . $occurrence['fileName'] . '"', $alternative);
                    }
                    $data['alternative_has_html'] = true;
               }
               $alternatives[$altIndex] = $alternative;
          }

          if (!isset($data['content']))
               $data['content'] = null;

          if (!isset($data['topic']))
               $data['topic'] = null;

          if (!isset($data['subtopic']))
               $data['subtopic'] = null;

          //Get id or create others models needed to create a question
          $arrayWithIds = $this->getModelsIdOrcreateNonExistentEntities([
               Matter::class => $data['matter'],
               Content::class => $data['content'],
               Topic::class => $data['topic'],
               Subtopic::class => $data['subtopic']
          ]);

          if (isset($arrayWithIds['Matter'])) {
               $data['matter_id'] = $arrayWithIds['Matter'];
               unset($data['matter']);
          }

          if (isset($arrayWithIds['Content'])) {
               $data['content_id'] = $arrayWithIds['Content'];
               unset($data['content']);
          }

          if (isset($arrayWithIds['Topic'])) {
               $data['topic_id'] = $arrayWithIds['Topic'];
               unset($data['topic']);
          }

          if (isset($arrayWithIds['Subtopic'])) {
               $data['subtopic_id'] = $arrayWithIds['Subtopic'];
               unset($data['subtopic']);
          }

          $data['year_id'] = $this->getYearOrcreateNonExistentYear($data['year']);
          unset($data['year']);

          $data['institution_id'] = $this->getInstitutionOrcreateNonExistentYear($data['institution']);
          unset($data['institution']);

          $data['query'] = $queryHtml;
          $data['code'] = $code;
          $data['alternatives'] = $alternatives;

          if (isset($alternatives[0]))
          {
               $data['alternative_a'] = $alternatives[0];
          }

          if (isset($alternatives[1]))
          {
               $data['alternative_b'] = $alternatives[1];
          }

          if (isset($alternatives[2]))
          {
               $data['alternative_c'] = $alternatives[2];
          }

          if (isset($alternatives[3]))
          {
               $data['alternative_d'] = $alternatives[3];
          }

          if (isset($alternatives[4])) {
               $data['alternative_e'] = $alternatives[4];
          }
          
          unset($data['alternatives']);

          $data['difficulty'] = $this->getDificultyByScriptTag($data['difficulty']);
          $data['state'] = ActiveState::ACTIVE->value;

          return Question::create($data);
     }

     protected function downloadAndSaveImagesReturningFileName(string $linkForDownload, string $questionCode): string
     {
          $response = Http::get($linkForDownload);

          if (!$response->successful()) {
               throw QuestionInventoryException::downloadException("Erro when tryning download image");
          }

          // $tempDirectory = storage_path("app/temp");
          $tempDirectory = sys_get_temp_dir();

          if (!is_dir($tempDirectory)) {
               mkdir($tempDirectory, 0755, true);
          }

          $originalName = basename($linkForDownload);
          $tempPath = "{$tempDirectory}/{$questionCode}_{$originalName}";

          file_put_contents($tempPath, $response->body());
          chmod($tempPath, 0666);

          $uploadedFile = new UploadedFile($tempPath, $originalName, null, null, true);
          $newFileName = $this->saveQuestionImage($uploadedFile, $questionCode);
          
          return $newFileName;
     }

     private function getDificultyByScriptTag(string $scriptTag)
     {
          return match ($scriptTag) {
               'Fácil' => Difficulty::EASY->value,
               'Médio' => Difficulty::INTERMEDIARY->value,
               'Difícil' => Difficulty::HARD->value,
               default => throw new \InvalidArgumentException("Invalid difficulty: $scriptTag"),
          };
     }

     private function getModelsIdOrcreateNonExistentEntities(array $entities): array
     {
          $ids = [];

          foreach ($entities as $modelClass => $value) {
               if (!empty($value)) {

                    if ($modelClass == "App\Models\Matter\Matter") {
                         $entity = $modelClass::firstOrCreate(['name' => $value]);
                         $ids[class_basename($modelClass)] = $entity->id;
                    }

                    if ($modelClass == "App\Models\Content\Content") {
                         $entity = $modelClass::where('name', $value)->first();
                         if (!$entity == null) {
                              $ids[class_basename($modelClass)] = $entity->id;
                         } else {
                              $contentForCreate = ['name' => $value, 'matter_id' => $ids['Matter']];
                              $contentCreated = $modelClass::create($contentForCreate);
                              $ids[class_basename($modelClass)] = $contentCreated->id;
                         }
                    }

                    if ($modelClass == "App\Models\Matter\Topic") {
                         $entity = $modelClass::where('name', $value)->first();
                         if (!$entity == null) {
                              $ids[class_basename($modelClass)] = $entity->id;
                         } else {
                              $topicForCreate = ['name' => $value, 'content_id' => $ids['Content']];
                              $topicCreated = $modelClass::create($topicForCreate);
                              $ids[class_basename($modelClass)] = $topicCreated->id;
                         }
                    }

                    if ($modelClass == "App\Models\Matter\Subtopic") {
                         $entity = $modelClass::where('name', $value)->first();
                         if (!$entity == null) {
                              $ids[class_basename($modelClass)] = $entity->id;
                         } else {
                              $subtopicForCreate = ['name' => $value, 'topic_id' => $ids['Topic']];
                              $subtopicCreated = $modelClass::create($subtopicForCreate);
                              $ids[class_basename($modelClass)] = $subtopicCreated->id;
                         }
                    }
               }
          }
          return $ids;
     }

     private function getYearOrcreateNonExistentYear(string $year)
     {
          if (!empty($year)) {
               return Year::firstOrCreate(['year' => $year])->id;
          }
     }

     private function getInstitutionOrcreateNonExistentYear(string $institution)
     {
          if (!empty($institution)) {
               return Institution::firstOrCreate(['name' => $institution])->id;
          }
     }

     /**
      * @throws QuestionInventoryException
      */
     public function update(array $data, int $questionId): void
     {
          try
          {
               if (isset($data['matter_id']))
               {
                    Matter::where('id', $data['matter_id'])->firstOrFail();
               }

               if (isset($data['content_id']))
               {
                    Content::where('id', $data['content_id'])->firstOrFail();
               }

               if (isset($data['topic_id']))
               {
                    Topic::where('id', $data['topic_id'])->firstOrFail();
               }

               if (isset($data['subtopic_id']))
               {
                    Subtopic::where('id', $data['subtopic_id'])->firstOrFail();
               }

               if (isset($data['institution_id']))
               {
                    Institution::where('id', $data['institution_id'])->firstOrFail();
               }

               if (isset($data['year_id']))
               {
                    Year::where('id', $data['year_id'])->firstOrFail();
               }

               $question = $this->getById($questionId);
     
               $question->fill($data);
     
               if ($question->isDirty()) {
                    $question->save();
               } else {
                    throw new QuestionInventoryException("No changes detected for update");
               }
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::notFound("", $ex->getMessage());
          }
     }

     /**
      * @throws QuestionInventoryException
      */
     public function delete(int $questionId): void
     {
          if ($questionId <= 0) {
               throw QuestionInventoryException::invalidId();
          }

          $question = $this->GetById($questionId);
          $question->delete();

          if ($question->image !== null) {
               $this->deleteImage($question->image);
          }
     }

     public function getAll(int $page, int $perPage = 15): LengthAwarePaginator
     {
          return Question::paginate($perPage, ['*'], 'page', $page);
     }

     public function getById(int $questionId)
     {
          return Question::findOrFail($questionId);
     }

     public function getByCode(string $code)
     {
          return Question::where('code', $code)->firstOrFail();
     }

     /**
      * @throws QuestionInventoryException
      */
     public function query(object $fields, int $page, int $perPage)
     {
          $query = Question::query();

          $query->when($fields->matter_id, function ($query, $matter_id) {
               $query->where('matter_id', $matter_id);
          });
          $query->when($fields->content_id, function ($query, $content_id) {
               $query->where('content_id', $content_id);
          });
          $query->when($fields->topic_id, function ($query, $topic_id) {
               $query->where('topic_id', $topic_id);
          });
          $query->when($fields->subtopic_id, function ($query, $subtopic_id) {
               $query->where('subtopic_id', $subtopic_id);
          });
          $query->when($fields->year_id, function ($query, $year_id) {
               $query->where('year_id', $year_id);
          });
          $query->when($fields->difficulty, function ($query, $difficulty) {
               $query->where('difficulty', 'LIKE', '%' . $difficulty . '%');
          });
          $query->when($fields->institution_id, function ($query, $institution_id) {
               $query->where('institution_id', $institution_id);
          });
          $query->when($fields->state, function ($query, $state) {
               $query->where('state', 'LIKE', '%' . $state . '%');
          });

          try {
               $paginatedResults = $query->paginate($perPage, ['*'], 'page', $page);
               // return $paginatedResults;

               $modifiedCollection = collect($paginatedResults->items())->map(function ($question) {
                    $fieldsToModify = ['query', 'alternative_a', 'alternative_b', 'alternative_c', 'alternative_d', 'alternative_e'];
            
                    foreach ($fieldsToModify as $field) {
                         if (!empty($question->{$field})) {
                              $awsUrlPath = $this->aws_url_path;
                              $awsImagesQuestionFolder = $this->aws_images_question_folder;
                     
                              $question->{$field} = preg_replace_callback(
                                   '/src=["\']([^"\']+)["\']/',
                                   function ($matches) use ($awsUrlPath, $awsImagesQuestionFolder) {
                                     return 'src="' . $awsUrlPath . '/' . $awsImagesQuestionFolder . '/' . ltrim($matches[1], '/') . '"';
                                   },
                                   $question->{$field}
                              );
                         }
                    }
            
                    return $question;
               });
            
                // Retornar a coleção paginada
               return new \Illuminate\Pagination\LengthAwarePaginator(
                    $modifiedCollection,
                    $paginatedResults->total(),
                    $paginatedResults->perPage(),
                    $paginatedResults->currentPage(),
                    ['path' => $paginatedResults->path()]
               );

          } catch (Exception $ex) {
               Log::error("Un erro occurred when tryning execute a query! - err: {$ex->getMessage()}");
               throw QuestionInventoryException::QueryError();
          }
     }

     /**
      * @throws QuestionInventoryException
      */
     protected function saveQuestionImage(UploadedFile $file, string $questionCode): string
     {
          $newFileName = $this->generateNewNamefile($file, $questionCode);
          $filePath = "{$this->aws_images_question_folder}/{$newFileName}";

          try 
          {
               $stream = fopen($file->getRealPath(), 'r+');

               Storage::disk('s3')->put($filePath, $stream, [
                    'visibility' => 'public',
                    'ContentType' => 'image/png',
               ]);

               return $newFileName;
          } 
          catch (QuestionInventoryException $ex) 
          {
               throw $ex;
          } 
          catch (Exception $ex) 
          {
               Log::error("Un erro occurred when tryning save file in folder! - err: {$ex->getMessage()}");
               throw new Exception($ex);
          }
     }

     private function generateNewNamefile(UploadedFile $file, string $questionCode): string
     {
          $extension = $file->getClientOriginalExtension();
          $randomName = Str::random(20) . '.' . $extension;
          return $questionCode . $randomName;
     }

     private function generateCode(): string
     {
          $randomCode = str_pad(rand(0, 99999), 10, '0', STR_PAD_LEFT);

          while (Question::where('code', $randomCode)->first()) {
               $randomCode = str_pad(rand(0, 99999), 10, '0', STR_PAD_LEFT);
          }

          return $randomCode;
     }

     /**
      * @throws QuestionInventoryException
      */
     private function deleteImage(string $fileName): void
     {
          $completedPath = public_path($this->aws_images_question_folder . '/' . $fileName);

          try {
               if (file_exists($completedPath)) {
                    unlink($completedPath);
               }
          } catch (Exception $ex) {
               throw QuestionInventoryException::deleteError();
          }
     }

     /**
      * @throws QuestionInventoryException
      */
     public function getAllFilters()
     {
          $matters = Matter::all();
          $contents = Content::all();
          $topics = Topic::all();
          $subtopics = Subtopic::all();
          $years = Year::all();
          $institutions = Institution::all();

          return $filters = [
               'matters' => $matters,
               'contents' => $contents,
               'topics' => $topics,
               'subtopics' => $subtopics,
               'years' => $years,
               'institutions' => $institutions
          ];
     }
}
