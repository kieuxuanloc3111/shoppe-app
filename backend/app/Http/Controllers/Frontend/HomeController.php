<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Products;

use App\Models\Category;
use App\Models\Brand;

class HomeController extends Controller
{
    public function index()
    {
        $products = Products::orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $categories = Category::all();
        $brands     = Brand::all();

        return view('frontend.home.index', compact(
            'products',
            'categories',
            'brands'
        ));
    }
}

