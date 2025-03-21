<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'slug' => $this->slug,
            'task_progress' => $this->whenLoaded('task_progress'),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
