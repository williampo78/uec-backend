<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function getById(int $id): Model;
    public function getByAccount(string $account): Model;
}

?>
