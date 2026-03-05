<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    // ================= INDEX =================
    public function index(Request $request)
    {
        $query = Products::with(['user','category','brand'])
            ->orderBy('id','desc');

        // Search theo tên member
        if ($request->filled('member_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name','like','%'.$request->member_name.'%');
            });
        }

        $products = $query->get();

        return view('admin.product.index', compact('products'));
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $product = Products::findOrFail($id);

        $categories = Category::all();
        $brands     = Brand::all();

        $oldImages = json_decode($product->image, true) ?? [];

        return view('admin.product.edit', compact(
            'product',
            'categories',
            'brands',
            'oldImages'
        ));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $dir = public_path('upload/product');
        $manager = new ImageManager(new Driver());

        $oldImages = json_decode($product->image, true) ?? [];
        $deleteImages = $request->delete_images ?? [];

        $remainingImages = array_values(array_diff($oldImages, $deleteImages));
        $newImages = [];

        // Upload ảnh mới
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {

                if (count($remainingImages) + count($newImages) >= 3) {
                    return back()->withErrors('Max 3 images');
                }

                if ($file->getSize() > 1024 * 1024) {
                    return back()->withErrors('Image > 1MB');
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
            return back()->withErrors('Product must have at least 1 image');
        }

        // Xóa ảnh cũ đã tick
        foreach ($deleteImages as $img) {
            if (in_array($img, $oldImages)) {
                @unlink($dir.'/'.$img);
                @unlink($dir.'/85x84_'.$img);
                @unlink($dir.'/329x380_'.$img);
            }
        }

        // Update product
        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'status'      => $request->status, // 0 = New, 1 = Sale
            'sale_price'  => $request->status == 1 ? $request->sale_price : null,
            'company'     => $request->company,
            'detail'      => $request->detail,
            'image'       => json_encode($finalImages),
        ]);

        return redirect()
            ->route('admin.product.index')
            ->with('success','Update success');
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $product = Products::findOrFail($id);

        $images = json_decode($product->image, true) ?? [];

        foreach ($images as $img) {
            @unlink(public_path('upload/product/'.$img));
            @unlink(public_path('upload/product/85x84_'.$img));
            @unlink(public_path('upload/product/329x380_'.$img));
        }

        $product->delete();

        return back()->with('success','Deleted');
    }
}