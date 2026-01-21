<?php

namespace Botble\ShortNews\Models;

use Botble\Base\Models\BaseModel;
use Botble\Blog\Models\Post;
use Botble\Base\Enums\BaseStatusEnum;

class ShortNews extends BaseModel
{
    /**
     * Get only posts that have Short News points defined.
     */
    public static function getActiveShorts($limit = 15)
    {
        return Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->whereHas('metadata', function ($query) {
                $query->where('meta_key', 'short_news_points')
                      ->where('meta_value', '!=', '');
            })
            ->with(['metadata'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}