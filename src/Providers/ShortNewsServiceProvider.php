<?php

namespace Botble\ShortNews\Providers;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Blog\Models\Post;
use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Forms\FormAbstract;

class ShortNewsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('plugins/short-news')
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/core/plugins/short-news'),
        ], 'public');

        $this->loadRoutes();

        // HOOK 1: Injects fields into the form builder
        add_filter(BASE_FILTER_BEFORE_RENDER_FORM, [$this, 'extendPostForm'], 120, 2);

        // HOOK 2: Saving metadata
        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, [$this, 'saveShortNewsFields'], 120, 3);
        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, [$this, 'saveShortNewsFields'], 120, 3);

        // Language Support
        if (is_plugin_active('language')) {
            add_filter('language_register_module', function (array $modules) {
                if (!in_array(Post::class, $modules)) {
                    $modules[] = Post::class;
                }
                return $modules;
            }, 120);

            add_filter('language_get_module_meta_boxes', function (array $boxes, string $model) {
                if ($model == Post::class) {
                    $boxes[] = 'short_news_additional_fields';
                }
                return $boxes;
            }, 120, 2);
        }

        // Shortcode registration
        add_shortcode('short-news-stories', 'Short News Stories', 'Display stories', function () {
            $stories = Post::query()
                ->wherePublished()
                ->latest()
                ->get()
                ->filter(fn($p) => !empty(trim($p->getMetaData('short_news_points', true))))
                ->values();

            if ($stories->isEmpty()) return null;

            $mappedStories = $stories->map(function ($post) {
                $rawPoints = $post->getMetaData('short_news_points', true);
                $pointsArray = $rawPoints ? explode("\n", str_replace("\r", "", $rawPoints)) : [];
                $vImage = $post->getMetaData('short_news_v_image', true) ?: $post->image;
                
                return [
                    'id'       => $post->id,
                    'title'    => $post->name,
                    'url'      => $post->url,
                    'image'    => RvMedia::getImageUrl($vImage),
                    'points'   => array_filter(array_map('trim', $pointsArray)),
                    'date'     => function_exists('dne_date_format') ? dne_date_format($post->created_at) : $post->created_at->format('M d, Y'),
                    'category' => $post->categories->first() ? $post->categories->first()->name : 'News',
                ];
            });

            return view('plugins/short-news::shortcodes.stories-combined', [
                'stories'       => $stories,
                'mappedStories' => $mappedStories
            ]);
        });

        // --- UPDATE CHECKER SYSTEM ---
        // We check for the constant to prevent errors if the base system isn't fully loaded
        if (defined('BASE_FILTER_CHECK_UPDATE_URL')) {
            if ($this->app->runningInConsole() || request()->is(config('core.base.general.admin_dir', 'admin') . '*')) {
                $this->app->booted(function () {
                    add_filter(BASE_FILTER_CHECK_UPDATE_URL, function ($urls) {
                        // Ensure this key 'short-news' matches your actual folder name in platform/plugins
                        $urls['short-news'] = 'https://raw.githubusercontent.com/amangirmat/short-news/refs/heads/main/update.json';
                        return $urls;
                    }, 120);
                });
            }
        }
    }

    
    public function extendPostForm(FormAbstract $form, $model): FormAbstract
    {
        // Ensure we are in admin and editing/creating a Post
        if (AdminHelper::isInAdmin(true) && $model instanceof Post) {
            
            $points = MetaBox::getMetaData($model, 'short_news_points', true);
            $vImage = MetaBox::getMetaData($model, 'short_news_v_image', true);

            // addMetaBoxes adds it to the bottom "Advanced" area
            $form->addMetaBoxes([
                'short_news_additional_fields' => [
                    'title'    => 'Short News Highlights',
                    'content'  => view('plugins/short-news::admin-fields', compact('points', 'vImage'))->render(),
                    'priority' => 'high',
                ],
            ]);
        }

        return $form;
    }

    /**
     * Save metadata for both main language and translations
     */
    public function saveShortNewsFields(string $type, Request $request, $object): void
    {
        if ($object instanceof Post) {
            if ($request->has('short_news_points')) {
                MetaBox::saveMetaBoxData($object, 'short_news_points', $request->input('short_news_points'));
            }
            if ($request->has('short_news_v_image')) {
                MetaBox::saveMetaBoxData($object, 'short_news_v_image', $request->input('short_news_v_image'));
            }
        }
    }

    
}