<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return[
        'id'=>$this->id,
        'name'=>$this->name,
        'start_time' => Carbon::parse($this->start_time)
                ->format('h:i A'),

        'end_time' => Carbon::parse($this->end_time)
                ->format('h:i A'),
        'hours' => $this->hours,

        'break_minutes' => $this->breaK_minute,

        'grace_minutes' => $this->grace_minutes,
        'status'        => $this->status

       ];
    }
}
