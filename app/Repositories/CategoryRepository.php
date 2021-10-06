<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CategoryRepository extends Repository implements CategoryRepositoryInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Category::class;
    }

    /**
     * @param  int  $primary_category_id
     * @return Model
     */
    public function getByCategoryId(int $primary_category_id): Model
    {
        return $this->model->where('primary_category_id', $primary_category_id)->firstOrFail();
    }

    public function getCategory(){
        $agent_id = Auth::user()->agent_id;
        $result = Category::select('category.*' , 'primary_category.name  as primary_category_name' , 'primary_category.number as primary_category_number')
                        ->where('category.agent_id' , $agent_id)
                        ->leftJoin('primary_category', 'primary_category.id' , '=' , 'category.primary_category_id')
                        ->orderBy('primary_category_number')
                        ->orderBy('category.number','asc');
        return $result;
    }

}

?>
