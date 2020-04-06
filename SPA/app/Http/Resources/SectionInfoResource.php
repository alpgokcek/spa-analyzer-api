<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SectionInfoLabelResource;

class SectionInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'section' => $this->section,
            'info' => $this->info,
            'labels' => SectionInfoLabelResource::collection($this->infoLabels)
        ];
    }
}
