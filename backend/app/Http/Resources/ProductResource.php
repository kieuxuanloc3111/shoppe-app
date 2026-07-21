<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'shop_id'     => $this->shop_id,
            'category_id' => $this->category_id,
            'brand_id'    => $this->brand_id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'price_min'   => $this->price_min,
            'price_max'   => $this->price_max,
            'sold_count'  => $this->sold_count,
            'rating_avg'  => $this->rating_avg,
            'images'      => $this->whenLoaded('images'),
            'variants'    => $this->whenLoaded('variants'),
            'options'     => $this->whenLoaded('options'),
            'shop'        => new ShopResource($this->whenLoaded('shop')),
        ];
    }
}
