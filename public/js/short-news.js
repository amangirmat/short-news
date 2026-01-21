/**
 * Short News Interactive Story Tray & Modal Logic
 * Handles Swiper initialization, Auto-play progress, 
 * Pull-to-close gestures, and "Seen" status.
 */

let swiper;
let startY = 0;
let isDragging = false;
let isPaused = false;

// 1. Keyboard Navigation
window.addEventListener('keydown', (e) => {
    const modal = document.getElementById('short-news-modal');
    if (!modal || !modal.classList.contains('active')) return;

    if (e.code === "Space") { 
        e.preventDefault(); 
        togglePause(); 
    } 
    else if (e.code === "ArrowUp") { 
        e.preventDefault(); 
        swiper.slidePrev(); 
    } 
    else if (e.code === "ArrowDown") { 
        e.preventDefault(); 
        swiper.slideNext(); 
    }
});

// 2. Load "Seen" Status from LocalStorage on Page Load
document.addEventListener('DOMContentLoaded', () => {
    const seenStories = JSON.parse(localStorage.getItem('seen_stories') || '[]');
    seenStories.forEach(id => {
        const circle = document.getElementById('home-circle-' + id);
        if (circle) circle.classList.add('is-seen');
    });
});

// 3. Swiper Initialization
function initSwiper() {
    swiper = new Swiper('.swiper', {
        direction: 'vertical',
        speed: 800,
        autoplay: { 
            delay: 7500, 
            disableOnInteraction: false 
        },
        resistanceRatio: 0,
        on: {
            slideChange() {
                isPaused = false;
                // Remove pause indicators from all slides
                document.querySelectorAll('.pause-indicator').forEach(el => el.classList.remove('active'));
                
                const activeSlide = this.slides[this.activeIndex];
                const storyId = activeSlide.getAttribute('data-story-id');
                
                if (storyId) {
                    markStoryAsSeen(storyId);
                }

                // Stop autoplay on Intro (0) and Outro (last) slides
                if (this.activeIndex === 0 || this.activeIndex === this.slides.length - 1) {
                    this.autoplay.stop();
                } else {
                    this.autoplay.start();
                }
            },
            autoplayTimeLeft(s, time, progress) {
                // Update the segmented progress bars at the top
                const activeSlide = s.slides[s.activeIndex];
                const fills = activeSlide.querySelectorAll('.p-fill');
                const currentStoryIndex = s.activeIndex - 1; // Subtracting 1 because of Intro slide
                
                if (currentStoryIndex >= 0 && fills[currentStoryIndex]) {
                    // Set all previous bars to 100%
                    for (let i = 0; i < currentStoryIndex; i++) {
                        if (fills[i]) fills[i].style.width = '100%';
                    }
                    // Animate current bar
                    fills[currentStoryIndex].style.width = (1 - progress) * 100 + '%';
                }
            }
        }
    });

    setupPullToClose();
}

// 4. Mobile Gesture: Pull to Close
function setupPullToClose() {
    const container = document.querySelector('.swiper');
    const pullTop = document.getElementById('pull-top');
    const pullBottom = document.getElementById('pull-bottom');

    container.addEventListener('touchstart', (e) => { 
        startY = e.touches[0].clientY; 
        isDragging = true; 
        container.style.transition = 'none'; 
    }, { passive: true });

    container.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        
        let diff = startY - e.touches[0].clientY;
        
        // Only trigger pull effect at the very top or very bottom
        if ((swiper.isBeginning && diff < 0) || (swiper.isEnd && diff > 0)) {
            let moveY = -diff * 0.4; // Friction effect
            container.style.transform = `translateY(${moveY}px)`;
            
            const target = diff < 0 ? pullTop : pullBottom;
            const label = target.querySelector('.pull-label');
            
            target.style.opacity = Math.min(Math.abs(diff) / 100, 1);
            target.style.transform = `translateY(${diff < 0 ? Math.abs(moveY) + 30 : -(Math.abs(moveY) + 30)}px)`;
            
            // Threshold for release (180px)
            if (Math.abs(diff) > 180) {
                label.innerText = typeof transExit !== 'undefined' ? transExit : "RELEASE TO CLOSE";
                label.classList.add('pull-release');
            } else {
                label.innerText = typeof transPull !== 'undefined' ? transPull : "PULL TO CLOSE";
                label.classList.remove('pull-release');
            }
        }
    }, { passive: true });

    container.addEventListener('touchend', (e) => {
        isDragging = false;
        let diff = startY - e.changedTouches[0].clientY;
        
        container.style.transition = 'transform 0.4s cubic-bezier(0.1, 0.9, 0.2, 1)';
        
        if (Math.abs(diff) > 180 && ((swiper.isBeginning && diff < 0) || (swiper.isEnd && diff > 0))) {
            closeShorts();
        } else {
            // Snap back
            container.style.transform = 'translateY(0)'; 
            if(pullTop) pullTop.style.opacity = 0; 
            if(pullBottom) pullBottom.style.opacity = 0; 
        }
    });
}

// 5. Open Modal Logic
function openShorts(id) {
    const modal = document.getElementById('short-news-modal');
    modal.style.display = 'block';
    
    // Trigger transition
    setTimeout(() => modal.classList.add('active'), 10);
    document.body.style.overflow = 'hidden';

    if (!swiper) {
        initSwiper();
    }

    // Find index of the story in the data array
    const idx = storiesData.findIndex(s => s.id == id);
    
    // Jump to intro slide first, then animate to target story
    swiper.slideTo(0, 0); 
    setTimeout(() => { 
        swiper.slideTo(idx + 1, 1000); 
    }, 50);

    markStoryAsSeen(id.toString());
}

// 6. Close Modal Logic
function closeShorts() {
    const modal = document.getElementById('short-news-modal');
    const pullTop = document.getElementById('pull-top');
    const pullBottom = document.getElementById('pull-bottom');

    modal.classList.remove('active');
    
    // Reset Pull UI
    [pullTop, pullBottom].forEach(el => {
        if (!el) return;
        el.style.opacity = 0;
        const label = el.querySelector('.pull-label');
        label.innerText = typeof transPull !== 'undefined' ? transPull : "PULL TO CLOSE";
        label.classList.remove('pull-release');
    });

    setTimeout(() => { 
        modal.style.display = 'none'; 
        document.querySelector('.swiper').style.transform = 'translateY(0)'; 
    }, 500);

    document.body.style.overflow = 'auto';
    if(swiper) swiper.autoplay.stop();
}

// 7. Data Persistence: Mark as Seen
function markStoryAsSeen(id) {
    const circle = document.getElementById('home-circle-' + id);
    if (circle) circle.classList.add('is-seen');

    let seen = JSON.parse(localStorage.getItem('seen_stories') || '[]');
    if (!seen.includes(id.toString())) {
        seen.push(id.toString());
        localStorage.setItem('seen_stories', JSON.stringify(seen));
    }
}

// 8. Video-style Pause/Play
function togglePause() {
    if (!swiper) return;
    
    const activeSlide = swiper.slides[swiper.activeIndex];
    const indicator = activeSlide.querySelector('.pause-indicator');
    
    if (!isPaused) { 
        swiper.autoplay.stop(); 
        isPaused = true; 
        if (indicator) indicator.classList.add('active'); 
    } 
    else { 
        swiper.autoplay.start(); 
        isPaused = false; 
        if (indicator) indicator.classList.remove('active'); 
    }
}