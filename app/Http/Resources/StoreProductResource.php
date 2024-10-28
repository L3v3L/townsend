<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'image' => $this->image,
            'id' => $this->id,
            'artist' => $this->artist->name,
            'title' => strlen($this->display_name) > 3 ? $this->display_name : $this->name,
            'description' => $this->description,
            'price' => $this->converted_price,
            'format' => $this->type,
            'release_date' => $this->release_date,
        ];
    }
}
