<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CanvasInfoLabelResource;

class CanvasInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'canvas' => $this->canvas,
            'info' => $this->info,
            'labels' => CanvasInfoLabelResource::collection($this->infoLabels)
        ];
    }
}
