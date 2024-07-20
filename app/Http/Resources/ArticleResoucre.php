<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResoucre extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'publish_date' => $this->publish_date,
        ];
    }
}
