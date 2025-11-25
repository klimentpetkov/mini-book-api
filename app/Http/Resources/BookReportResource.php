<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'book_id'      => $this->book_id,
            'summary'      => $this->summary,
            'stats'        => $this->stats,
            'generated_at' => optional($this->generated_at)?->toIso8601String(),
            'created_at'   => optional($this->created_at)?->toIso8601String(),
            'updated_at'   => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
