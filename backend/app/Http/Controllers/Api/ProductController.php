<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class ProductController extends Controller
{
    //
    public $successStatus = 200;
    public function product(){
        $getProductHome = Products::orderBy('updated_at', 'desc')
                            ->take(6)
                            ->get();
        return response()->json([
            'response' => 'success',
            'data' => $getProductHome
        ],$this->successStatus);
    }

    public function categoryBrand()
    {
        $category = Category::select('id', 'name as category')->get();
        $brand = Brand::select('id', 'name as brand')->get();

        return response()->json([
            'category' => $category,
            'brand' => $brand
        ]);
    }
    public function addProduct(Request $request)
    {
        $dir = public_path('upload/product');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $manager = new ImageManager(new Driver());

        $images = [];

        if ($request->hasFile('file')) {

            foreach ($request->file('file') as $file) {

                $name = time().'_'.$file->getClientOriginalName();

                $image = $manager->read($file);

                $image->save($dir.'/'.$name);

                $images[] = $name;
            }
        }

        $product = Products::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'sale' => $request->status,
            'sale_price' => $request->sale,
            'company' => $request->company,
            'detail' => $request->detail,
            'user_id' => $request->user_id,
            'image' => json_encode($images)
        ]);

        return response()->json([
            'response' => 'success',
            'data' => $product
        ]);
    }
}
