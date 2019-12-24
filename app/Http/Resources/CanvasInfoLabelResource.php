<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CanvasInfoLabelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'info' => $this->info,
            'type' => $this->type,
            'label' => $this->label,
        ];
    }
}
