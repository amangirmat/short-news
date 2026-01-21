<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Today's News Highlights</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        :root { --primary: #e74c3c; --bg: #000; }
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: var(--bg); overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

        .short-news-container { width: 100%; height: 100vh; display: flex; justify-content: center; align-items: center; position: relative; background: #000; }

        /* PC Outer Navigation */
        .side-nav { position: absolute; top: 0; bottom: 0; width: calc((100% - 95vh * 9/16) / 2); z-index: 2000; cursor: pointer; display: none; align-items: center; justify-content: center; color: rgba(255,255,255,0.1); font-size: 3rem; transition: 0.3s; }
        .side-nav:hover { color: #fff; background: rgba(255,255,255,0.03); }
        .side-nav-left { left: 0; } .side-nav-right { right: 0; }
        @media (min-width: 768px) { .side-nav { display: flex; } }

        /* Swiper Frame */
        .swiper { width: 100%; height: 100%; background: #000; position: relative; z-index: 10; }
        @media (min-width: 768px) { .swiper { width: auto; aspect-ratio: 9 / 16; max-height: 92vh; border-radius: 20px; border: 6px solid #222; } }

        .swiper-slide { position: relative; width: 100%; height: 100%; overflow: hidden; display: flex; flex-direction: column; justify-content: center; }
        
        /* Background & Ken Burns */
        .short-bg { position: absolute; inset: 0; z-index: 1; }
        .short-bg img { width: 100%; height: 100%; object-fit: cover; transition: transform 8s linear; }
        .swiper-slide-active .short-bg img { transform: scale(1.15); }
        .is-paused .short-bg img { transform: scale(1.05) !important; transition: none !important; }

        .short-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.95) 8%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.6) 100%); z-index: 2; }

        /* Content & Brand Line */
        .short-content { position: absolute; bottom: 0; left: 0; width: 100%; padding: 40px 25px 50px; box-sizing: border-box; z-index: 100; pointer-events: none; }
        .short-content * { pointer-events: auto; }
        .brand-line { border-left: 5px solid var(--primary); padding-left: 15px; margin-bottom: 15px; }
        .short-title { font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1.2; margin: 0; }
        
        /* Metadata & Points */
        .meta-data { font-size: 0.75rem; color: rgba(255,255,255,0.8); margin-bottom: 10px; display: flex; gap: 10px; align-items: center; }
        .meta-category { background: var(--primary); color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: bold; text-transform: uppercase; }
        
        .highlight-list { list-style: none; padding: 0; margin: 15px 0 25px; }
        .highlight-list li { margin-bottom: 10px; font-size: 1rem; color: #ffffff !important; display: flex; line-height: 1.4; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
        .highlight-list li span { color: var(--primary); margin-right: 12px; font-weight: bold; }

        .read-more { display: block; background: var(--primary); color: #fff; text-align: center; padding: 14px; border-radius: 10px; text-decoration: none; font-weight: bold; margin-top: 20px; }

        /* Navigation Zones Inside Frame */
        .inner-nav-layer { position: absolute; top: 0; left: 0; width: 100%; height: 75%; z-index: 50; display: flex; }
        .nav-zone { height: 100%; cursor: pointer; }
        .nav-prev { width: 25%; }
        .nav-pause { width: 50%; display: flex; align-items: center; justify-content: center; }
        .nav-next { width: 25%; }

        .pause-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); border-radius: 50%; display: none; align-items: center; justify-content: center; z-index: 60; }
        .pause-icon::after, .pause-icon::before { content: ''; width: 6px; height: 25px; background: #fff; margin: 0 4px; border-radius: 10px; }

        /* Progress Bars */
        .progress-container { position: absolute; top: 15px; left: 15px; right: 15px; display: flex; gap: 4px; z-index: 200; }
        .p-bar { flex: 1; height: 3px; background: rgba(255,255,255,0.2); border-radius: 10px; overflow: hidden; }
        .p-fill { height: 100%; background: #fff; width: 0%; }

        .close-btn { position: absolute; top: 20px; right: 20px; color: #fff; font-size: 35px; z-index: 3000; text-decoration: none; }
        #loader { position: fixed; inset: 0; background: #000; z-index: 9999; display: flex; align-items: center; justify-content: center; }
        .spinner { width: 40px; height: 40px; border: 3px solid rgba(255,255,255,0.1); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div id="loader"><div class="spinner"></div></div>

<div class="short-news-container" id="main-container">
    <a href="javascript:void(0)" onclick="smartBack()" class="close-btn">&times;</a>

    <div class="side-nav side-nav-left" onclick="swiper.slidePrev()">&#10094;</div>
    <div class="side-nav side-nav-right" onclick="swiper.slideNext()">&#10095;</div>

    <div class="swiper">
        <div class="swiper-wrapper">
            
            <div class="swiper-slide">
                <div class="short-bg">
                    @if($stories->first()) <img src="{{ $stories->first()['image'] }}" alt=""> @endif
                    <div class="short-overlay"></div>
                </div>
                <div class="inner-nav-layer">
                    <div class="nav-zone nav-prev" onclick="swiper.slidePrev()"></div>
                    <div class="nav-zone nav-pause" onclick="togglePause()"></div>
                    <div class="nav-zone nav-next" onclick="swiper.slideNext()"></div>
                </div>
                <div class="short-content" style="text-align: center; bottom: 15%;">
                    <div class="brand-line" style="border-left:none; border-bottom: 4px solid var(--primary); display: inline-block; padding: 0 20px 10px; margin-bottom: 20px;">
                        <h1 style="font-size: 2.5rem; margin:0; color:#fff; text-transform: uppercase;">Today's News<br>Highlights</h1>
                    </div>
                </div>
            </div>

            @foreach($stories as $story)
                <div class="swiper-slide">
                    <div class="progress-container">
                        @foreach($stories as $s) <div class="p-bar"><div class="p-fill"></div></div> @endforeach
                    </div>
                    
                    <div class="inner-nav-layer">
                        <div class="nav-zone nav-prev" onclick="swiper.slidePrev()"></div>
                        <div class="nav-zone nav-pause" onclick="togglePause()">
                            <div class="pause-icon"></div>
                        </div>
                        <div class="nav-zone nav-next" onclick="swiper.slideNext()"></div>
                    </div>

                    <div class="short-bg"><img src="{{ $story['image'] }}" alt=""><div class="short-overlay"></div></div>
                    
                    <div class="short-content">
                        <div class="meta-data">
                            <span class="meta-category">{{ $story['category'] ?? 'News' }}</span>
<span>{{ $story['date'] }}</span>                        </div>
                        <div class="brand-line"><h2 class="short-title">{{ $story['title'] }}</h2></div>
                        <ul class="highlight-list">
                            @foreach($story['points'] as $point)
                                <li><span>✦</span> {{ $point }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ $story['url'] }}" class="read-more">Read Full Article</a>
                    </div>
                </div>
            @endforeach

            <div class="swiper-slide" style="background:#111;">
                <div class="inner-nav-layer">
                    <div class="nav-zone nav-prev" onclick="swiper.slidePrev()"></div>
                    <div class="nav-zone nav-pause" onclick="togglePause()"></div>
                    <div class="nav-zone nav-next" onclick="swiper.slideNext()"></div>
                </div>
                <div class="short-content" style="text-align: center; bottom: 40%;">
                    <div class="brand-line" style="border-left:none; border-bottom: 4px solid var(--primary); display: inline-block; padding: 0 20px 10px; margin-bottom: 30px;">
                        <h2 style="font-size: 2.2rem; margin: 0; color:#fff;">All Caught Up</h2>
                    </div>
                    <a href="javascript:void(0)" onclick="swiper.slideTo(0)" class="read-more" style="display:inline-block; padding: 12px 40px; background:#fff; color:#000;">Replay from Start</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    window.onload = () => document.getElementById('loader').style.display = 'none';

    function smartBack() {
        if (document.referrer.indexOf(window.location.host) !== -1) window.history.back();
        else window.location.href = "{{ url('/') }}";
    }

    // --- DEEP LINKING & SMOOTH TRANSITION ---
    const storiesData = @json($stories);
    const urlParams = new URLSearchParams(window.location.search);
    const startId = urlParams.get('id');

    let targetIndex = 0; 
    if (startId) {
        const foundIndex = storiesData.findIndex(item => item.id == startId);
        if (foundIndex !== -1) {
            targetIndex = foundIndex + 1; // +1 for the Cover Slide
        }
    }

    const swiper = new Swiper('.swiper', {
        direction: 'vertical',
        mousewheel: true,
        threshold: 20,
        initialSlide: 0, // Always start at 0 for the transition effect
        speed: 800,      // Speed of the slide movement
        autoplay: { delay: 7500, disableOnInteraction: false },
        on: {
            init: function() {
                // If the user clicked a specific circle, slide to it smoothly
                if(targetIndex > 0) {
                    setTimeout(() => {
                        this.slideTo(targetIndex, 1000); // 1-second slide animation
                    }, 400); 
                } else {
                    // Stop autoplay if we stay on the cover slide
                    this.autoplay.stop();
                }
            },
            autoplayTimeLeft(s, time, progress) {
                const currentSlide = s.slides[s.activeIndex];
                const fills = currentSlide.querySelectorAll('.p-fill');
                if(!fills.length) return;
                
                const storyIdx = s.activeIndex - 1; 
                if (storyIdx < 0) return;

                for(let i = 0; i < storyIdx; i++) if(fills[i]) fills[i].style.width = '100%';
                if(fills[storyIdx]) fills[storyIdx].style.width = (1 - progress) * 100 + '%';
            },
            slideChange() {
                const container = document.getElementById('main-container');
                container.classList.remove('is-paused');
                document.querySelectorAll('.p-fill').forEach(el => el.style.width = '0%');
                document.querySelectorAll('.pause-icon').forEach(el => el.style.display = 'none');
                
                // Toggle autoplay based on slide type (don't play on cover or end slide)
                if(this.activeIndex === 0 || this.activeIndex === this.slides.length - 1) {
                    this.autoplay.stop();
                } else {
                    this.autoplay.start();
                }

                const currentStory = storiesData[this.activeIndex - 1]; // -1 because of cover slide
    if (currentStory && currentStory.id) {
        let seenStories = JSON.parse(localStorage.getItem('seen_short_news') || '[]');
        if (!seenStories.includes(currentStory.id)) {
            seenStories.push(currentStory.id);
            localStorage.setItem('seen_short_news', JSON.stringify(seenStories));
        }
    }
            },
            touchEnd(s) {
                if (s.activeIndex === 0 && s.swipeDirection === 'prev' && Math.abs(s.touches.diff) > 70) smartBack();
                if (s.activeIndex === s.slides.length - 1 && s.swipeDirection === 'next' && Math.abs(s.touches.diff) > 70) s.slideTo(0);
            }
        }
    });

    function togglePause() {
        const container = document.getElementById('main-container');
        const activeSlide = swiper.slides[swiper.activeIndex];
        const icon = activeSlide.querySelector('.pause-icon');
        if (!icon) return;
        
        if (swiper.autoplay.paused) {
            swiper.autoplay.resume();
            container.classList.remove('is-paused');
            icon.style.display = 'none';
        } else {
            swiper.autoplay.pause();
            container.classList.add('is-paused');
            icon.style.display = 'flex';
        }
    }
</script>
</body>
</html>