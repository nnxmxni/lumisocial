<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SprintMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource(User::find($this->pivot->user_id)),
            'is_admin' => (bool)$this->pivot->is_admin,
            'is_creator' => (bool)$this->pivot->is_creator,
            'created_at' => $this->pivot->created_at,
            'updated_at' => $this->pivot->updated_at
        ];
    }
}
