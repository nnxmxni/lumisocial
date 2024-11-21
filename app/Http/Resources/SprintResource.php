<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Enum\SprintStatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SprintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusAndCountdownArray = $this->getStatusAndCountDownAttribute($this->start_at, $this->end_at);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'goal' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'status' => $statusAndCountdownArray['status'],
            'countdownInDays' => $statusAndCountdownArray['countdown'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members' => SprintMemberResource::collection($this->members),
            'tasks' => TaskResource::collection($this->tasks)
        ];
    }

    private function getStatusAndCountDownAttribute(): array
    {
        $now = Carbon::now()->toDate();

        if ($this->is_completed) {
            $data['status'] = SprintStatusEnum::COMPLETED->value;
            $data['countdown'] = 0;
            return $data;
        }

        if (Carbon::parse($this->start_at) > $now) {
            $data['status'] = SprintStatusEnum::INCOMING->value;
            $data['countdown'] = round(Carbon::parse($this->start_at)->diffInDays($now));
            return $data;
        }

        if (Carbon::parse($this->end_at) < $now) {
            $data['status'] = SprintStatusEnum::OVERDUE->value;
            $value = -round(Carbon::parse($this->end_at)->diffInDays($now));
            $data['countdown'] = -$value;
            return $data;
        }

        $data['status'] = SprintStatusEnum::PENDING->value;
        $data['countdown'] = round(Carbon::parse($now)->diffInDays($this->end_at));
        return $data;
    }
}
