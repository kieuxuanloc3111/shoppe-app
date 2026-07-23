<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'name'       => $this->name,
            'unit_price' => $this->unit_price,
            'qty'        => $this->qty,
            'line_total' => $this->line_total,
        ];
    }
}
