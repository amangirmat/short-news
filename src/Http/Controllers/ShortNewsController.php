<?php

namespace Botble\ShortNews\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Blog\Models\Post;
use Illuminate\Http\Request;

class ShortNewsController extends BaseController
{
    public function index(Request $request)
{
    // Remove the 2-day limit temporarily to see if your stories reappear
    // We also use 'values()' to reset the array keys after filtering
    $posts = Post::query()
        ->wherePublished()
        ->latest()
        ->get()
        ->filter(function($post) {
            $points = $post->getMetaData('short_news_points', true);
            // Check if points exist and aren't just whitespace
            return !empty($points) && strlen(trim($points)) > 5;
        })
        ->values(); // Important: resets indexes for Swiper

    $startId = $request->query('id');

    $stories = $posts->map(function ($post) {
        $rawPoints = $post->getMetaData('short_news_points', true);
        $pointsArray = $rawPoints ? explode("\n", str_replace("\r", "", $rawPoints)) : [];
        $vImage = $post->getMetaData('short_news_v_image', true) ?: $post->image;

        return [
            'id'         => $post->id,
            'title'      => $post->name,
            'url'        => $post->url,
            'image'      => \RvMedia::getImageUrl($vImage),
            'points'     => array_filter(array_map('trim', $pointsArray)),
            'date'       => dne_date_format($post->created_at),
            'category'   => $post->categories->first() ? $post->categories->first()->name : 'News',
        ];
    });

    return view('plugins/short-news::frontend-slider', compact('stories', 'startId'));
}
}