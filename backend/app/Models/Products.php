<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Products extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'shop_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'rating_avg' => 'decimal:2',
    ];

    // giá hiển thị (khoảng giá của các variant) đi kèm khi trả JSON
    protected $appends = ['price_min', 'price_max'];

    protected static function booted(): void
    {
        static::creating(function (Products $p) {
            if (empty($p->slug)) {
                $p->slug = static::uniqueSlug($p->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $i = 2;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /* ===== quan hệ ===== */

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // FK chỉ định rõ vì model tên 'Products' (số nhiều) làm Eloquent đoán sai 'products_id'
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class, 'product_id');
    }

    /* ===== khoảng giá từ variants ===== */

    public function getPriceMinAttribute()
    {
        return $this->variants->min('price');
    }

    public function getPriceMaxAttribute()
    {
        return $this->variants->max('price');
    }
}
