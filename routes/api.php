<?php

use App\Http\Controllers\MatterController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserQuestionAnsweredController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\SubtopicController;

// Rotas públicas (sem autenticação)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/password/forgot', [AuthController::class, 'sendPasswordResetLink']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/questions/create/script', [QuestionController::class, 'script']);

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Rotas de matérias
    Route::resource('/matter', MatterController::class);
    Route::post('/matter/name', [MatterController::class, 'getByName'])
        ->name('getByName');

    // Rotas de questões
    Route::controller(QuestionController::class)->group(function () {
        // Rotas de leitura - todos os usuários autenticados podem acessar
        Route::get('/questions', 'index');
        Route::get('/questions/id/{id}', 'show');
        Route::get('/questions/code/{code}', 'getByCode');
        Route::get('/questions/query', 'query');
        Route::get('/questions/filters', 'getAllFilters');

        // Rotas de modificação - apenas admin e suporte
        Route::post('/questions', 'store');
        Route::put('/questions/{id}', 'update');
        Route::delete('/questions/{id}', 'destroy');
    });

    // Rotas de respostas dos usuários
    Route::controller(UserQuestionAnsweredController::class)->group(function () {
        // Rotas para usuários verem suas próprias respostas
        Route::get('/users/answers', 'indexUserQuestionAnswereds');
        Route::post('/users/answers', 'storeUserQuestionAnswered');
        Route::put('/users/{userId}/answers/{questionId}', 'updateUserQuestionAnswered');
        Route::get('/users/comments', 'indexUserQuestionComments');
        Route::post('/users/comments', 'storeUserQuestionComment');
        Route::get('/users/annotations', 'indexUserQuestionAnnotation');
        Route::post('/users/annotations', 'storeUserQuestionAnnotation');

        // Rotas que requerem permissões especiais
        Route::get('/users/user/{userId}/answers', 'getUserQuestionAnsweredByUserId');
        Route::get('/users/question/{questionId}/answers', 'getUserQuestionAnsweredByQuestionId');
        Route::delete('/users/{userId}/answers/{questionId}', 'destroyUserQuestionAnswered');

        Route::get('/users/user/{userId}/comments', 'getUserQuestionCommentByUserId');
        Route::get('/users/question/{questionId}/comments', 'getUserQuestionCommentByQuestionId');
        Route::put('/users/comments/{id}', 'updateUserQuestionComment');
        Route::delete('/users/comments/{id}', 'destroyUserQuestionComment');

        Route::get('/users/user/{userId}/annotations', 'getUserQuestionAnnotationByUserId');
        Route::get('/users/question/{questionId}/annotations', 'getUserQuestionAnnotationByQuestionId');
        Route::get('/users/{user}/{questionId}/annotations', 'getUserQuestionAnnotationByUserAndQuestionId');
        Route::put('/users/annotations/{id}', 'updateUserQuestionAnnotation');
        Route::delete('/users/annotations/{id}', 'destroyUserQuestionAnnotation');
    });

    Route::controller(YearController::class)->group(function () {
        Route::get('/years/{yearId}', 'getById');
    });

    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken'])->middleware(['ability:refresh_token']);

    //Rotas referente a Usuario
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{email}', [UserController::class, 'findUserByEmail']);
});

Route::resource('/content',ContentController::class);

Route::post('/content/name', [ContentController::class, 'getByName'])->name('content.getByName');

Route::controller(TopicController::class)->group(function () {
    Route::post('/topics', 'store');
    Route::get('/topics/{id}', 'show');
    Route::get('/topics/search/{name}', 'searchByName');
    Route::get('/topics', 'index');

    // Rotas de modificação - apenas admin e suporte
    Route::post('/topics', 'store');
    Route::put('/topics/{id}', 'update');
    Route::delete('/topics/{id}', 'destroy');
});

Route::controller(SubtopicController::class)->group(function () {
    Route::post('/subtopics', 'store');
    Route::get('/subtopics/{id}', 'show');
    Route::get('/subtopics/search/{name}', 'searchByName');
    Route::get('/subtopics', 'index');

    // Rotas de modificação - apenas admin e suporte
    Route::post('/subtopics', 'store');
    Route::put('/subtopics/{id}', 'update');
    Route::delete('/subtopics/{id}', 'destroy');
});
