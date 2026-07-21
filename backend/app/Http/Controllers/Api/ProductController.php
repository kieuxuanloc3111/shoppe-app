<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductOption;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public $successStatus = 200;

    /* ============ CÔNG KHAI ============ */

    // trang chủ: 6 sản phẩm mới
    public function product()
    {
        $products = Products::where('is_active', true)
            ->with(['images', 'variants'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        return response()->json(['response' => 'success', 'data' => $products], $this->successStatus);
    }

    public function categoryBrand()
    {
        return response()->json([
            'category' => Category::select('id', 'name as category')->get(),
            'brand'    => Brand::select('id', 'name as brand')->get(),
        ]);
    }

    public function detail($id)
    {
        $product = Products::with(['images', 'variants', 'options', 'shop', 'category', 'brand'])->find($id);

        if (!$product) {
            return response()->json(['response' => 'error', 'message' => 'Product not found'], 404);
        }

        $product->increment('view_count');

        return response()->json(['response' => 'success', 'data' => $product], $this->successStatus);
    }

    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $products = $keyword
            ? Products::where('is_active', true)
                ->where('name', 'LIKE', '%' . $keyword . '%')
                ->with(['images', 'variants'])
                ->get()
            : collect();

        return response()->json([
            'response' => 'success',
            'keyword'  => $keyword,
            'data'     => $products,
        ], $this->successStatus);
    }

    public function advancedSearch(Request $request)
    {
        $query = Products::where('is_active', true)
            ->with(['images', 'variants'])
            ->latest('updated_at');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->price_range);
            $query->whereHas('variants', fn ($q) => $q->whereBetween('price', [$min, $max]));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        return response()->json([
            'response'   => 'success',
            'data'       => $query->paginate(6)->withQueryString(),
            'categories' => Category::all(),
            'brands'     => Brand::all(),
        ], $this->successStatus);
    }

    // lọc theo khoảng giá variant
    public function filterPrice(Request $request)
    {
        $products = Products::where('is_active', true)
            ->whereHas('variants', fn ($q) => $q->whereBetween('price', [$request->min, $request->max]))
            ->with(['images', 'variants'])
            ->latest('updated_at')
            ->get();

        return response()->json(['response' => 'success', 'data' => $products], $this->successStatus);
    }

    // xem trước giỏ hàng (theo product) — ponytail: sẽ đổi sang theo variant ở T6
    public function productCart(Request $request)
    {
        $out = [];
        foreach ((array) $request->cart as $productId => $qty) {
            $product = Products::with(['images', 'variants'])->find($productId);
            if ($product) {
                $out[] = [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => $product->price_min,
                    'image' => optional($product->images->first())->url ?? '',
                    'qty'   => $qty,
                ];
            }
        }

        return response()->json(['response' => 'success', 'data' => $out], $this->successStatus);
    }

    /* ============ NGƯỜI BÁN (seller) ============ */

    public function myProduct()
    {
        $shop = auth()->user()->shop;

        $products = $shop
            ? $shop->products()->with(['images', 'variants'])->latest('id')->get()
            : collect();

        return response()->json(['response' => 'success', 'data' => $products], $this->successStatus);
    }

    // form sửa: chỉ chủ shop
    public function getProduct($id)
    {
        $product = Products::with(['images', 'variants', 'options'])->find($id);

        if (!$product) {
            return response()->json(['response' => 'error', 'message' => 'Product not found'], 404);
        }
        if ($product->shop_id !== auth()->user()->shop->id) {
            return response()->json(['response' => 'error', 'message' => 'Permission denied'], 403);
        }

        return response()->json(['response' => 'success', 'data' => $product], $this->successStatus);
    }

    public function addProduct(Request $request)
    {
        $shop = auth()->user()->shop;

        $data = $this->validateProduct($request);

        $product = DB::transaction(function () use ($request, $shop, $data) {
            $product = Products::create([
                'shop_id'     => $shop->id,
                'category_id' => $data['category_id'],
                'brand_id'    => $data['brand_id'],
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $this->syncOptions($product, $data['options']);
            $this->syncVariants($product, $data['variants']);
            $this->uploadImages($product, $request);

            return $product;
        });

        return response()->json([
            'response' => 'success',
            'data'     => $product->load(['images', 'variants', 'options']),
        ], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json(['response' => 'error', 'message' => 'Product not found'], 404);
        }
        if ($product->shop_id !== auth()->user()->shop->id) {
            return response()->json(['response' => 'error', 'message' => 'Permission denied'], 403);
        }

        $data = $this->validateProduct($request);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update([
                'category_id' => $data['category_id'],
                'brand_id'    => $data['brand_id'],
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            // thay toàn bộ option + variant (đơn giản cho MVP)
            $product->options()->delete();
            $product->variants()->delete();
            $this->syncOptions($product, $data['options']);
            $this->syncVariants($product, $data['variants']);

            // xóa ảnh được đánh dấu + thêm ảnh mới
            $this->removeImages($product, (array) $request->input('remove_images', []));
            $this->uploadImages($product, $request);
        });

        return response()->json([
            'response' => 'success',
            'data'     => $product->fresh()->load(['images', 'variants', 'options']),
        ], $this->successStatus);
    }

    public function deleteProduct($id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json(['response' => 'error', 'message' => 'Product not found'], 404);
        }
        if ($product->shop_id !== auth()->user()->shop->id) {
            return response()->json(['response' => 'error', 'message' => 'Permission denied'], 403);
        }

        $product->delete(); // soft delete — giữ ảnh để có thể khôi phục

        return response()->json(['response' => 'success'], $this->successStatus);
    }

    /* ============ helper ============ */

    // options/variants có thể tới dưới dạng JSON string (multipart) → decode
    private function validateProduct(Request $request): array
    {
        $request->merge([
            'options'  => $this->asArray($request->input('options')),
            'variants' => $this->asArray($request->input('variants')),
        ]);

        return $request->validate([
            'name'                    => 'required|string|max:255',
            'category_id'             => 'required|exists:categories,id',
            'brand_id'                => 'required|exists:brands,id',
            'description'             => 'nullable|string',
            'variants'                => 'required|array|min:1',
            'variants.*.price'        => 'required|numeric|min:0',
            'variants.*.stock'        => 'required|integer|min:0',
            'variants.*.sku'          => 'nullable|string',
            'variants.*.option_values'=> 'nullable|array',
            'options'                 => 'nullable|array',
            'options.*.name'          => 'required_with:options|string',
            'options.*.values'        => 'required_with:options|array',
        ]);
    }

    private function asArray($v): array
    {
        if (is_array($v)) return $v;
        if (is_string($v)) return json_decode($v, true) ?: [];
        return [];
    }

    private function syncOptions(Products $product, ?array $options): void
    {
        foreach ($options ?? [] as $opt) {
            ProductOption::create([
                'product_id' => $product->id,
                'name'       => $opt['name'],
                'values'     => $opt['values'],
            ]);
        }
    }

    private function syncVariants(Products $product, array $variants): void
    {
        foreach ($variants as $v) {
            ProductVariant::create([
                'product_id'    => $product->id,
                'sku'           => $v['sku'] ?? null,
                'price'         => $v['price'],
                'stock'         => $v['stock'],
                'option_values' => $v['option_values'] ?? null,
            ]);
        }
    }

    private function uploadImages(Products $product, Request $request): void
    {
        if (!$request->hasFile('file')) return;

        $dir = public_path('upload/product');
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $manager = new ImageManager(new Driver());
        $order = (int) $product->images()->max('sort_order');

        foreach ($request->file('file') as $file) {
            $name = time() . '_' . $file->getClientOriginalName();
            $manager->read($file)->save($dir . '/' . $name);
            ProductImage::create([
                'product_id' => $product->id,
                'url'        => $name,
                'sort_order' => ++$order,
            ]);
        }
    }

    private function removeImages(Products $product, array $imageIds): void
    {
        $images = $product->images()->whereIn('id', $imageIds)->get();
        foreach ($images as $img) {
            $path = public_path('upload/product/' . $img->url);
            if (file_exists($path)) unlink($path);
            $img->delete();
        }
    }
}
