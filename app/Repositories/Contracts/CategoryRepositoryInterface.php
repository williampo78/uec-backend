<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CategoryRepositoryInterface
{
    public function getById(int $id): Model;
}

?>
