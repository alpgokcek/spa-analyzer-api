<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CanvasInfoResource;

class CanvasResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'infos' => CanvasInfoResource::collection($this->canvasInfos)
        ];
    }
}
