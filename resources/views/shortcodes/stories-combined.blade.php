{{-- Load Assets --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="{{ asset('vendor/core/plugins/short-news/css/short-news.css') }}">

<div class="stories-container-main">
    <div class="stories-title-side"><h3>{!! trans('plugins/short-news::short-news.todays_highlights') !!}</h3></div>
    <div class="stories-tray">
        @foreach($stories as $story)
            <div class="story-circle-item" onclick="openShorts({{ $story->id }})">
                <div class="circle-wrapper" id="home-circle-{{ $story->id }}">
                    <div class="seen-badge">{{ trans('plugins/short-news::short-news.seen') }}</div>
                    <div class="circle-inner">
                        @php $vImg = $story->getMetaData('short_news_v_image', true) ?: $story->image; @endphp
                        <img src="{{ RvMedia::getImageUrl($vImg, 'thumb') }}">
                    </div>
                </div>
                <span class="story-title-label">{{ Str::limit($story->name, 12) }}</span>
            </div>
        @endforeach
    </div>
</div>

<div id="short-news-modal">
    <div id="pull-top" class="pull-exit-indicator">
        <svg class="chevron-anim" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="transform: rotate(180deg)"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
        <span class="pull-label">{{ trans('plugins/short-news::short-news.pull_to_close') }}</span>
    </div>

    <div class="short-news-container">
        <div class="close-btn" onclick="closeShorts()">&times;</div>
        <div class="side-nav side-nav-left" onclick="swiper.slidePrev()">&#10094;</div>
        <div class="side-nav side-nav-right" onclick="swiper.slideNext()">&#10095;</div>

        <div class="swiper">
            <div class="swiper-wrapper">
                {{-- Intro Slide --}}
                <div class="swiper-slide" style="justify-content: center; align-items: center; text-align: center;">
                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" style="max-width: 160px; height: auto; margin-bottom: 25px;">
                    <h1 style="color:#fff; text-transform:uppercase; font-size: 2.2rem; margin: 0;">{!! trans('plugins/short-news::short-news.todays_highlights') !!}</h1>
                    <p style="margin-top: 20px; color: var(--text-dim);">{{ trans('plugins/short-news::short-news.swipe_up_to_read') }}</p>
                </div>

                @foreach($mappedStories as $story)
                <div class="swiper-slide" data-story-id="{{ $story['id'] }}">
                    <div class="progress-container">@foreach($mappedStories as $s) <div class="p-bar"><div class="p-fill"></div></div> @endforeach</div>
                    <div class="short-top-visual">
                        <div class="inner-nav-layer">
                            <div class="nav-zone nav-prev" onclick="swiper.slidePrev()"></div>
                            <div class="nav-zone nav-pause" onclick="togglePause()"></div>
                            <div class="nav-zone nav-next" onclick="swiper.slideNext()"></div>
                        </div>
                        <img src="{{ $story['image'] }}">
                        <div class="curve-divider"></div>
                    </div>
                    <div class="short-bottom-content">
                        <div class="brand-line"><h2 class="short-title">{{ $story['title'] }}</h2></div>
                        <ul class="highlight-list">
                            @foreach($story['points'] as $p) <li><span>✦</span> {{ $p }}</li> @endforeach 
                        </ul>
                        <a href="{{ $story['url'] }}" class="read-more-btn" target="_blank">{{ trans('plugins/short-news::short-news.read_full') }}</a>
                    </div>
                </div>
                @endforeach

                {{-- Outro Slide --}}
                <div class="swiper-slide" style="justify-content: center; align-items: center; text-align: center;">
                    <h2 style="color:#fff;">{{ trans('plugins/short-news::short-news.caught_up') }}</h2>
                    <p style="color:var(--text-dim);">{{ trans('plugins/short-news::short-news.caught_up_desc') }}</p>
                    <a href="{{ route('public.index') }}" class="read-more-btn">{{ trans('plugins/short-news::short-news.explore_more') }}</a>
                    <a href="javascript:void(0)" onclick="swiper.slideTo(0)" style="color:#fff; display:block; margin-top:20px;">{{ trans('plugins/short-news::short-news.replay') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    {{-- Pass data to external JS --}}
    const storiesData = @json($mappedStories);
    const transExit = "{{ trans('plugins/short-news::short-news.release_to_close') }}";
    const transPull = "{{ trans('plugins/short-news::short-news.pull_to_close') }}";
</script>
<script src="{{ asset('vendor/core/plugins/short-news/js/short-news.js') }}"></script>