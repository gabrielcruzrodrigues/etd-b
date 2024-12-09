<?php

namespace App\Contracts;

use App\Models\User\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceContract
{
    /**
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User;
    public function getUsers(int $perPage = 30): LengthAwarePaginator;
}
