<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    // Trả cây danh mục: các danh mục gốc kèm toàn bộ con cháu
    public function index()
    {
        $tree = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();

        return response()->json([
            'response' => 'success',
            'data'     => CategoryResource::collection($tree),
        ]);
    }
}
