<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array = [
            'name' => $this->name,
            'color' => $this->color,
            'status' => $this->status,
        ];

        if ($this->parent_id === null) {
            $array['categories'] = CategoryResource::collection($this->categories);
        }

        return $array;
    }
}
