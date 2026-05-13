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

            'sale' => (int) $request->status,
            'status' => (int) $request->status,

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
        
        $data = $request->cart; // 🔥 đúng key

        $getProduct = [];

        foreach ($data as $key => $value) {

            $product = Products::find($key);

            if($product){

                $image = json_decode($product->image, true);

                $getProduct[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'id_user' => $product->id_user,
                    'image' => $image[0] ?? '',
                    'qty' => $value
                ];
            }
        }

        return response()->json([
            'response' => 'success',
            'data' => $getProduct
        ], $this->successStatus);
    }

    public function getProduct($id){
        $product = Products::find($id);
        if (!$product){
                return response()->json([
                'response' => 'error',
                'message' => 'Product not found'
            ],404);
        }
        return response() -> json([
            'response' => 'success',
            'data' =>[
                'id' => $product ->id,
                'name' => $product->name,
                'price' => $product->price,
                'id_category' => $product->category_id,
                'id_brand' => $product->brand_id,
                'status' => $product->sale,
                'sale' => $product->sale_price,
                'company_profile' => $product->company,
                'detail' => $product->detail,
                'image' => json_decode($product->image,true)
            ]
        ]);
        
    }
    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();

        $product = Products::findOrFail($id);

        if ($product->user_id != $user->id) {
            return response()->json([
                'response' => 'error',
                'message' => 'Permission denied'
            ], 403);
        }

        $dir = public_path('upload/product');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $oldImages = json_decode($product->image, true) ?? [];

        // ảnh bị xoá
        $deleteImages = $request->avatarCheckBox ?? [];

        $finalImages = array_values(array_diff($oldImages, $deleteImages));

        // xoá file vật lý
        foreach ($deleteImages as $img) {

            $path = $dir . '/' . $img;

            if (file_exists($path)) {
                unlink($path);
            }
        }

        // upload ảnh mới
        if ($request->hasFile('file')) {

            foreach ($request->file('file') as $file) {

                $filename = time() . '_' . $file->getClientOriginalName();

                $file->move($dir, $filename);

                $finalImages[] = $filename;
            }
        }

        // update db
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category,
            'brand_id' => $request->brand,

            // status sản phẩm
            'status' => $request->status,

            // sale yes/no
            'sale' => $request->status == 0 ? 1 : 0,

            // giá sale
            'sale_price' => $request->sale,

            'company' => $request->company,
            'detail' => $request->detail,

            'image' => json_encode($finalImages)
        ]);

        $product->refresh();

        return response()->json([
            'response' => 'success',
            'data' => $product
        ], 200);
    }
}
