<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variants = new VariantCollection($this->variants);
        $images = new ProductImageCollection($this->images);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'stock_total' => $this->stock_total,
            'rating_average' => $this->rating_average,
            'amount_sold' => $this->amount_sold,
            'variants' => $variants,
            'images' => $images
        ];
    }
}
