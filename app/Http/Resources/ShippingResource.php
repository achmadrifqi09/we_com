<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DeliveryServiceResource;
use Illuminate\Support\Facades\Log;

class ShippingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_service' => new DeliveryServiceResource($this->delivery_service),
            'receipt_number' => $this->receipt_number,
            'status' => $this->status
        ];
    }
}
