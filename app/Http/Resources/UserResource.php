<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'signature' => $this->signature,
            'department_id' => $this->department_id,
            'file_number' => $this->file_number,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'cid' => $this->cid,
            'role' => $this->role,
            'supervisor_id' => $this->supervisor_id,
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
