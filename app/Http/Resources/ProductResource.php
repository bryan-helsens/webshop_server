<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'image_url' => $this->image ?: null,
            'price' => $this->price,
            'max_qty' => $this->quantity,
            'status' => $this->stock_status,
            'published' => (bool)$this->published,
            'category' => $this->category->slug,
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
