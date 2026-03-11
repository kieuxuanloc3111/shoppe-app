<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
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
    public function myProduct()
    {
        $user = Auth::user();

        $getProduct = Products::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'response' => 'success',
            'data' => $getProduct
        ], $this->successStatus);
    }
    public function deleteProduct($id)
    {
        $user = Auth::user();

        $product = Products::findOrFail($id);

        // bảo mật: chỉ xóa sản phẩm của mình
        if ($product->user_id != $user->id) {
            return response()->json([
                'response' => 'error',
                'message' => 'Permission denied'
            ], 403);
        }

        // xóa ảnh
        $images = json_decode($product->image, true);

        if ($images) {
            foreach ($images as $img) {

                $path = public_path('upload/product/'.$img);

                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $product->delete();

        return response()->json([
            'response' => 'success'
        ], $this->successStatus);
    }
    public function detail($id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'response' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'response' => 'success',
            'data' => $product
        ], $this->successStatus);
    }
    public function productCart(Request $request) {
        
        $data = $request->json()->all();
        
        $getProduct = [];
        foreach ($data as $key => $value) {
            $get = Products::findOrFail($key)->toArray();
            $get['qty'] = $value;
            $getProduct[] = $get;
        }
        return response()->json([
            'response' => 'success',
            'data' => $getProduct
        ], $this->successStatus);
    }
}
