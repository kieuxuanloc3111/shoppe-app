<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'logo'            => $this->logo,
            'description'     => $this->description,
            'status'          => $this->status,
            'rating_avg'      => $this->rating_avg,
            'followers_count' => $this->followers_count,
        ];
    }
}
