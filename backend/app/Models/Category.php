<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'commission_rate',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    // slug tự sinh khi tạo (giữ nguyên khi sửa tên)
    protected static function booted(): void
    {
        static::creating(function (Category $c) {
            if (empty($c->slug)) {
                $c->slug = static::uniqueSlug($c->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // đệ quy: nạp con của con... (dựng cây)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
