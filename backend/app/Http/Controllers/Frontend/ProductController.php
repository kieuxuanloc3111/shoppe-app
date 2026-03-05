<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\ProductRequest;
use App\Models\Products;
use App\Models\Category;
use App\Models\Brand;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view('frontend.product.add', compact('categories', 'brands'));
    }

    public function store(ProductRequest $request)
    {
        $dir = public_path('upload/product');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $images = [];

        $manager = new ImageManager(new Driver());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {

                $name = time() . '_' . $file->getClientOriginalName();

                $fullPath   = public_path('upload/product/' . $name);
                $smallPath  = public_path('upload/product/85x84_' . $name);
                $mediumPath = public_path('upload/product/329x380_' . $name);

                $image = $manager->read($file);

                $image->save($fullPath);
                $image->resize(85, 84)->save($smallPath);
                $image->resize(329, 380)->save($mediumPath);

                $images[] = $name;
            }
        }

        Products::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'sale'        => $request->sale,
            'sale_price'  => $request->sale_price,
            'company'     => $request->company,
            'detail'      => $request->detail,
            'status'      => 0, 
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'image'       => json_encode($images),
            'user_id'     => auth()->id(),
        ]);


        return back()->with('success', 'Add product success');
    }
    public function myProduct()
    {
        $products = Products::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.product.my_product', compact('products'));
    }

    public function edit($id)
    {
        $product = Products::findOrFail($id);

        $categories = Category::all();
        $brands     = Brand::all();

        $oldImages  = json_decode($product->image, true);

        return view('frontend.product.edit', compact(
            'product',
            'categories',
            'brands',
            'oldImages'
        ));
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $dir = public_path('upload/product');
        $manager = new ImageManager(new Driver());

        $oldImages = json_decode($product->image, true) ?? [];

        $deleteImages = $request->delete_images ?? [];

        $remainingImages = array_values(array_diff($oldImages, $deleteImages));

        $newImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {

                if (count($remainingImages) + count($newImages) >= 3) {
                    return back()->withErrors('Tổng số hình không được vượt quá 3');
                }

                if ($file->getSize() > 1024 * 1024) {
                    return back()->withErrors('Hình vượt quá 1MB');
                }

                $name = time().'_'.$file->getClientOriginalName();

                $image = $manager->read($file);
                $image->save($dir.'/'.$name);
                $image->resize(85,84)->save($dir.'/85x84_'.$name);
                $image->resize(329,380)->save($dir.'/329x380_'.$name);

                $newImages[] = $name;
            }
        }

        $finalImages = array_merge($remainingImages, $newImages);

        if (count($finalImages) < 1) {
            return back()->withErrors('Sản phẩm phải có ít nhất 1 ảnh');
        }

        foreach ($deleteImages as $img) {
            if (in_array($img, $oldImages)) {
                @unlink($dir.'/'.$img);
                @unlink($dir.'/85x84_'.$img);
                @unlink($dir.'/329x380_'.$img);
            }
        }

        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'sale'        => $request->sale,
            'sale_price'  => $request->sale == 1 ? $request->sale_price : null,
            'company'     => $request->company,
            'detail'      => $request->detail,
            'image'       => json_encode($finalImages),
        ]);

        return back()->with('success', 'cap nhat thanh cong');
    }
    public function destroy($id)
    {
        $product = Products::findOrFail($id);

        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $images = json_decode($product->image, true) ?? [];

        foreach ($images as $img) {
            @unlink(public_path('upload/product/'.$img));
            @unlink(public_path('upload/product/329x380/'.$img));
            @unlink(public_path('upload/product/85x84/'.$img));
        }

        $product->delete();

        return back()->with('success', 'Delete product success');
    }

    public function detail($id)
    {
        $product = Products::findOrFail($id);

        $images = json_decode($product->image, true) ?? [];

        return view('frontend.product.detail', compact(
            'product',
            'images'
        ));
    }    
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        if (!$keyword) {
            return redirect()->route('home');
        }

        $products = Products::query()
                    ->where('name', 'LIKE', '%' . $keyword . '%')
                    ->get();

        return view('frontend.product.search', compact(
            'products',
            'keyword'
        ));
    }
   
    public function advancedSearch(Request $request)
    {
        $query = Products::orderBy('updated_at', 'desc');


        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }


        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->price_range);
            $query->whereBetween('price', [$min, $max]);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'sale') {
                $query->where('sale', 1);
            } elseif ($request->status == 'new') {
                $query->where('sale', 0);
            }
        }

        $products = $query->paginate(6)->withQueryString();

        $categories = Category::all();
        $brands     = Brand::all();

        return view('frontend.home.index', compact(
            'products',
            'categories',
            'brands'
        ));
    }

    public function filterPrice(Request $request)
    {
        $min = $request->min;
        $max = $request->max;

        $products = Products::whereRaw(
            "(CASE 
                WHEN sale_price > 0 
                THEN price - (price * sale_price / 100)
                ELSE price 
            END) BETWEEN ? AND ?",
            [$min, $max]
        )
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('frontend.product.product_list',
            compact('products')
        )->render();
    }
}
