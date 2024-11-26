<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\FixitTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use FixitTrait;

    public function getAllCategories()
    {
        $categories = Category::all();
        return $this->SuccessResponse($categories,'All categories',200);
    }

}
