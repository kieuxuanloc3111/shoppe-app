<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    // Mở gian hàng — user hiện tại, chưa có shop
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->shop) {
            return response()->json([
                'response' => 'error',
                'message'  => 'Bạn đã có gian hàng',
            ], 409);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|string',
        ]);

        $shop = Shop::create([
            'user_id'     => $user->id,
            'name'        => $data['name'],
            'slug'        => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'logo'        => $data['logo'] ?? null,
            // status mặc định 'active' (xem migration); thực tế cần admin duyệt
        ]);

        $shop->refresh(); // nạp default từ DB (status) vào response

        return response()->json([
            'response' => 'success',
            'data'     => $shop,
        ], 201);
    }

    // Trang gian hàng công khai
    public function show(string $slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        return response()->json([
            'response' => 'success',
            'data'     => $shop,
        ]);
    }

    // Seller sửa gian hàng của chính mình (slug giữ nguyên, không đổi theo tên)
    public function update(Request $request)
    {
        $shop = auth()->user()->shop;

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|string',
        ]);

        $shop->update($data);

        return response()->json([
            'response' => 'success',
            'data'     => $shop,
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'shop';
        $slug = $base;
        $i = 2;
        while (Shop::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
