<style>
    .stories-container-main { max-width: 1200px; margin: 20px auto; padding: 0 15px; display: flex; align-items: center; gap: 20px; }
    .stories-title-side { flex: 0 0 auto; border-left: 4px solid #e74c3c; padding-left: 12px; }
    .stories-title-side h3 { margin: 0; font-size: 1.1rem; text-transform: uppercase; color: #333; line-height: 1.2; }
    .stories-tray { flex: 1; display: flex; gap: 15px; padding: 10px 0; overflow-x: auto; scrollbar-width: none; }
    .stories-tray::-webkit-scrollbar { display: none; }
    
    .story-circle-item { flex: 0 0 auto; width: 75px; text-align: center; text-decoration: none; cursor: pointer; }
    
    /* Colorful border for New stories */
    .circle-wrapper { 
        width: 66px; height: 66px; padding: 2.5px; border-radius: 50%; 
        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); 
        margin: 0 auto 5px; 
        transition: all 0.3s ease;
    }

    /* Grey border for Seen stories */
    .circle-wrapper.is-seen {
        background: #d1d1d1 !important;
    }
    .circle-wrapper.is-seen img {
        filter: grayscale(30%);
    }

    .circle-inner { width: 100%; height: 100%; border-radius: 50%; border: 2px solid #fff; overflow: hidden; }
    .circle-inner img { width: 100%; height: 100%; object-fit: cover; }
    
    .story-title-label { font-size: 10px; color: #444; font-weight: 600; display: block; }
    .seen-label { display: none; color: #999; font-size: 8px; font-weight: normal; }
    .circle-wrapper.is-seen + .story-title-label .seen-label { display: inline; }

    @media (max-width: 600px) { .stories-container-main { flex-direction: column; align-items: flex-start; } }
</style>

<div class="stories-container-main">
    <div class="stories-title-side"><h3>Today's<br>Highlights</h3></div>
    <div class="stories-tray">
        @foreach($stories as $story)
            @php $vImage = $story->getMetaData('short_news_v_image', true) ?: $story->image; @endphp
            <a href="{{ route('public.short-news') }}?id={{ $story->id }}" 
               class="story-circle-item" 
               onclick="markAsSeen({{ $story->id }})">
                <div class="circle-wrapper" id="story-circle-{{ $story->id }}">
                    <div class="circle-inner">
                        <img src="{{ RvMedia::getImageUrl($vImage, 'thumb') }}">
                    </div>
                </div>
                <span class="story-title-label">
                    {{ Str::limit($story->name, 10) }} <br>
                    <span class="seen-label">Seen</span>
                </span>
            </a>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const seenStories = JSON.parse(localStorage.getItem('seen_short_news') || '[]');
        seenStories.forEach(id => {
            const circle = document.getElementById('story-circle-' + id);
            if (circle) circle.classList.add('is-seen');
        });
    });

    function markAsSeen(id) {
        let seenStories = JSON.parse(localStorage.getItem('seen_short_news') || '[]');
        if (!seenStories.includes(id)) {
            seenStories.push(id);
            localStorage.setItem('seen_short_news', JSON.stringify(seenStories));
        }
    }
</script>