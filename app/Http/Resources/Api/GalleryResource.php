<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'file_url' => $this->file_url,
            'type' => $this->type,
            'file_size' => $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'mime_type' => $this->mime_type,
            'event' => new EventResource($this->whenLoaded('event')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
