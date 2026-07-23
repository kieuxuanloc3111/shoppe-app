<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'order_id'     => $this->order_id,
            'shop_id'      => $this->shop_id,
            'subtotal'     => $this->subtotal,
            'shipping_fee' => $this->shipping_fee,
            'status'       => $this->status,
            'items'        => OrderItemResource::collection($this->whenLoaded('items')),
            'fee'          => $this->whenLoaded('fee'),
            // thông tin nhận hàng (dùng cho màn seller) — khi order được nạp
            'receiver'     => $this->whenLoaded('order', fn () => [
                'name'    => $this->order->receiver_name,
                'phone'   => $this->order->receiver_phone,
                'address' => $this->order->receiver_address,
            ]),
        ];
    }
}
