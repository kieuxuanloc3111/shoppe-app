<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'buyer_id'         => $this->buyer_id,
            'receiver_name'    => $this->receiver_name,
            'receiver_phone'   => $this->receiver_phone,
            'receiver_address' => $this->receiver_address,
            'grand_total'      => $this->grand_total,
            'payment_status'   => $this->payment_status,
            'payment_method'   => $this->payment_method,
            'placed_at'        => $this->placed_at,
            'shop_orders'      => ShopOrderResource::collection($this->whenLoaded('shopOrders')),
        ];
    }
}
