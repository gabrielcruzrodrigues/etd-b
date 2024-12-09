<?php

namespace App\Services\User;

use App\Contracts\UserServiceContract;
use App\Exceptions\UserException;
use App\Models\User\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService implements UserServiceContract
{

    /**
     * @param string $email
     * @return User|null
     * @throws UserException
     */
    public function findUserByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        if(!$user){
            throw UserException::UserNotFound();
        }

        return $user;
    }
    public function getUsers(int $perPage = 30): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }
}
