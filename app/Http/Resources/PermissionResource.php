<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Permission;
class PermissionResource extends JsonResource
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
            'date' => $this->date->format('Y-m-d'),
            'approved_permissions_count' => $this->user->monthlyPermissionsCount(date:$this->date, status:'approved'),
            'translated_approved_permissions_count' => __('Approved Permissions in') . ' ' . __($this->date->format('F')) . ' ' . __($this->date->format('Y')),
            'time' => $this->time->format('H:i'),
            'reason' => $this->reason,
            'duration' => $this->duration,
            'type' => $this->type,
            'translated_type' => __($this->type),
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