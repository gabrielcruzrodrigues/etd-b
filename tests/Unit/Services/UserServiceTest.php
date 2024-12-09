<?php


use App\Exceptions\UserException;
use App\Models\User\User;
use App\Services\User\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->userService = new UserService();
    $this->email = 'test@example.com';
    $this->user = User::factory()->create(['email' => $this->email]);
});

describe('UserService',function(){

    describe('index', function () {
        it('returns paginated users', function () {
            User::factory()->count(50)->create();

            $result = $this->userService->getUsers(30);

            expect($result)->toBeInstanceOf(LengthAwarePaginator::class)
                ->and($result->count())->toBe(30)
                ->and($result->total())->toBe(51);
        });
    });
    describe('findUserByEmail',function(){
        it('finds a user by email', function () {

            $foundUser = $this->userService->findUserByEmail($this->email);

            expect($foundUser->id)->toBe($this->user->id);
        });

        it('throws UserException if user not found', function () {
            $nonexistentEmail = 'nonexistent@example.com';

            expect( fn() =>
            $this->userService->findUserByEmail($nonexistentEmail))
                ->toThrow(UserException::class);
        });
    });
});
