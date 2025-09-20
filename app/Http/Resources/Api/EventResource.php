<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'event_date_time' => $this->event_date_time,
            'video' => $this->video,
            'banner_image' => $this->banner_image ? config('filesystems.disks.s3.url') . '/' . $this->banner_image : null,
            'other_information' => $this->other_information,
            'djs' => DJResource::collection($this->whenLoaded('djs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}