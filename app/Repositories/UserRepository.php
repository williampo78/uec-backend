<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * @param  string  $account
     * @return Model
     */
    public function getByAccount(string $account): Model
    {
        return $this->model->where('email', $account)->firstOrFail();
    }

}

?>
