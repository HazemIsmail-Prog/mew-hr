<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'direction' => $this->direction,
            'translated_direction' => __($this->direction),
            'reason' => $this->reason,
            'status' => $this->status,
            'translated_status' => __($this->status),
            'notes' => $this->notes,
            'approved_by' => $this->approved_by,
            'approved_by_user' => new UserResource($this->whenLoaded('approvedByUser')),
            'approved_at' => $this->approved_at,
            'rejected_by' => $this->rejected_by,
            'rejected_by_user' => new UserResource($this->whenLoaded('rejectedByUser')),
            'rejected_at' => $this->rejected_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 