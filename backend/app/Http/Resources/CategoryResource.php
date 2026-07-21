<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'parent_id'       => $this->parent_id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'commission_rate' => $this->commission_rate,
            'children'        => CategoryResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
