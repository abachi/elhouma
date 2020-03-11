<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Report extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reporter_id' => $this->reporter_id,
            'description' => $this->description,
            'picture' => asset('storage/'.$this->picture),
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
