<?php
namespace App\Providers;

use App\Contracts\ContentServiceContract;
use App\Contracts\MatterServiceContract;
use App\Contracts\UserServiceContract;
use App\Services\Content\ContentService;
use App\Services\Matter\MatterService;
use App\Contracts\QuestionServiceContract;
use App\Contracts\UserQuestionAnnotationServiceContract;
use App\Contracts\UserQuestionAnsweredServiceContract;
use App\Contracts\UserQuestionCommentServiceContract;
use App\Contracts\AuthServiceContract;
use App\Contracts\TopicServiceContract;
use App\Services\Topic\TopicService;
use App\Services\Question\QuestionService;
use App\Services\User\UserQuestionAnnotationService;
use App\Services\User\UserQuestionAnsweredService;
use App\Services\User\UserQuestionCommentService;
use App\Services\User\AuthService;
use App\Services\User\UserService;
use Dotenv\Dotenv;
use Illuminate\Support\ServiceProvider;
use App\Services\Subtopic\SubtopicService;
use App\Contracts\SubtopicServiceContract;
use App\Contracts\YearServiceContract;
use App\Services\Question\YearService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceContract::class, UserService::class);
        $this->app->bind(AuthServiceContract::class, AuthService::class);
        $this->app->bind(MatterServiceContract::class, MatterService::class);
        $this->app->bind(QuestionServiceContract::class, QuestionService::class);
        $this->app->bind(ContentServiceContract::class, ContentService::class);
        $this->app->bind(UserQuestionAnsweredServiceContract::class, UserQuestionAnsweredService::class);
      	$this->app->bind(TopicServiceContract::class, TopicService::class);
        $this->app->bind(SubtopicServiceContract::class, SubtopicService::class);
        $this->app->bind(UserQuestionCommentServiceContract::class, UserQuestionCommentService::class);
        $this->app->bind(UserQuestionAnnotationServiceContract::class, UserQuestionAnnotationService::class);
        $this->app->bind(YearServiceContract::class, YearService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (file_exists(base_path('.env'))) {
            Dotenv::createImmutable(base_path())->load();
        }
    }
}
