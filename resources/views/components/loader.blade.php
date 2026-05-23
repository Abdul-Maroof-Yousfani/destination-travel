@props(['showOnLoad' => false])

<div id="global-loader" style="{{ $showOnLoad ? 'display: block;' : 'display: none;' }}">
    <div class="loader-overlay">
        
        <div class="flip-card">
            <div class="flip-inner" id="flipInner">
                <div class="face front"><img id="frontImg" src="{{ asset('assets/images/loader/animation1.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80'"></div>
                <div class="face back" ><img id="backImg"  src="{{ asset('assets/images/loader/animation2.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1527838832700-5059252407fa?w=800&q=80'"></div>
            </div>
            <div class="collage-overlay" id="collage">
                <div class="cell"><img src="{{ asset('assets/images/loader/animation1.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&q=80'"></div>
                <div class="cell"><img src="{{ asset('assets/images/loader/animation2.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1527838832700-5059252407fa?w=400&q=80'"></div>
                <div class="cell"><img src="{{ asset('assets/images/loader/animation3.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1496588152823-86ff7695e68f?w=400&q=80'"></div>
                <div class="cell"><img src="{{ asset('assets/images/loader/animation4.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=400&q=80'"></div>
            </div>
        </div>
        
        <div class="loader-text-container">
            <div class="loader-text" id="loadingText">
                <span class="loading-word">{{ $showOnLoad ? 'Loading' : 'Searching' }}</span>
                <span class="dots"><span>.</span><span>.</span><span>.</span></span>
            </div>
        </div>

    </div>
</div>

<style>
    :root {
        --cube-size: 240px;
        --half-size: 120px;
    }
    
    @media (max-width: 768px) {
        :root {
            --cube-size: 180px;
            --half-size: 90px;
        }
    }

    #global-loader {
        position: fixed;
        inset: 0;
        z-index: 999999;
    }

    .loader-overlay {
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.634);
        backdrop-filter: blur(10px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 28px;
    }

    .flip-card {
        width: var(--cube-size);
        height: var(--cube-size);
        position: relative;
        perspective: 900px;
    }

    .flip-inner {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 1.1s cubic-bezier(0.645, 0.045, 0.355, 1.000);
    }

    .face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: inset 0 0 15px rgba(0,0,0,0.1), 0 10px 25px rgba(0,0,0,0.15);
    }
    .face img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .front { transform: rotateY(0deg); }
    .back { transform: rotateY(180deg); }

    .collage-overlay {
        position: absolute;
        inset: 0;
        opacity: 0;
        border-radius: 16px;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 3px;
        z-index: 10;
        background: #ffffff;
        pointer-events: none;
        transform: scale(0.88);
        box-shadow: inset 0 0 15px rgba(0,0,0,0.1), 0 10px 25px rgba(0,0,0,0.15);
    }
    .collage-overlay .cell { overflow: hidden; }
    .collage-overlay .cell img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .collage-overlay .cell:nth-child(1) { border-radius: 14px 0 0 0; }
    .collage-overlay .cell:nth-child(2) { border-radius: 0 14px 0 0; }
    .collage-overlay .cell:nth-child(3) { border-radius: 0 0 0 14px; }
    .collage-overlay .cell:nth-child(4) { border-radius: 0 0 14px 0; }

    .collage-overlay.show { animation: revealCollage 1.1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .collage-overlay.hide { animation: hideCollage 0.7s cubic-bezier(0.55, 0, 1, 0.45) forwards; }

    @keyframes revealCollage {
        0%   { opacity: 0; transform: scale(0.78) rotateY(8deg); }
        50%  { opacity: 1; }
        75%  { transform: scale(1.04) rotateY(-2deg); }
        100% { opacity: 1; transform: scale(1) rotateY(0deg); }
    }
    @keyframes hideCollage {
        0%   { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.88) rotateY(-6deg); }
    }

    /* Loader Text */
    .loader-text-container {
        text-align: center;
        z-index: 10;
        height: 30px; /* To prevent layout shift when text fades */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loader-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #0d7a8c;
        font-size: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
        letter-spacing: 2px;
        text-transform: uppercase;
        visibility: visible;
        transition: opacity 0.5s ease;
    }
    .loader-text.fading { opacity: 0; }

    @media (max-width: 768px) {
        .loader-text { font-size: 16px; }
    }

    .dots span {
        animation: blink 1.6s ease-in-out infinite;
        opacity: 0;
    }
    .dots span:nth-child(2) { animation-delay: 0.25s; }
    .dots span:nth-child(3) { animation-delay: 0.5s; }
    @keyframes blink { 0%, 100% { opacity: 0; } 45% { opacity: 1; } }
</style>

<script>
    const loaderImages = [
        "{{ asset('assets/images/loader/animation1.jpg') }}",
        "{{ asset('assets/images/loader/animation2.jpg') }}",
        "{{ asset('assets/images/loader/animation3.jpg') }}",
        "{{ asset('assets/images/loader/animation4.jpg') }}"
    ];
    let isFlipped = false, flipTimers = [], isRunning = false;

    function stopCycle() {
        isRunning = false;
        flipTimers.forEach(timer => clearTimeout(timer));
        flipTimers = [];
    }

    function runCycle() {
        if(isRunning) return;
        isRunning = true;

        const inner    = document.getElementById("flipInner");
        const frontImg = document.getElementById("frontImg");
        const backImg  = document.getElementById("backImg");
        const col      = document.getElementById("collage");
        const txt      = document.getElementById("loadingText");

        if(!inner || !frontImg || !backImg || !col || !txt) {
            isRunning = false;
            return;
        }

        isFlipped = false;
        inner.style.transition = "none";
        inner.style.transform  = "rotateY(0deg)";
        col.classList.remove("show","hide");
        col.style.opacity = "0";
        txt.classList.remove("fading");
        txt.style.visibility = "visible";

        frontImg.src = loaderImages[0];
        backImg.src  = loaderImages[1];

        const FD = 1100, HOLD = 1100;

        function doFlip(idx) {
            isFlipped = !isFlipped;
            inner.style.transition = `transform ${FD}ms cubic-bezier(0.645,0.045,0.355,1.000)`;
            inner.style.transform  = isFlipped ? "rotateY(-180deg)" : "rotateY(0deg)";
            flipTimers.push(setTimeout(() => {
                if(idx+1 < loaderImages.length) {
                    if(isFlipped) frontImg.src = loaderImages[idx+1];
                    else          backImg.src  = loaderImages[idx+1];
                }
            }, FD+60));
        }

        let t = HOLD;
        for(let i=1; i<loaderImages.length; i++) {
            const idx = i;
            flipTimers.push(setTimeout(() => doFlip(idx), t));
            t += FD + HOLD;
        }

        // text smooth fade out
        flipTimers.push(setTimeout(() => {
            txt.classList.add("fading");
        }, t));

        // collage reveal
        flipTimers.push(setTimeout(() => {
            col.classList.add("show");
        }, t+550));

        // collage hide
        flipTimers.push(setTimeout(() => {
            col.classList.remove("show");
            col.classList.add("hide");
            txt.style.visibility = "visible";
            txt.classList.remove("fading");
        }, t+2800));

        // restart cycle
        flipTimers.push(setTimeout(() => {
            col.classList.remove("hide");
            col.style.opacity = "0";
            isRunning = false;
            flipTimers  = [];
            // Recursively start again if still visible
            if ($('#global-loader').is(':visible')) {
                runCycle();
            }
        }, t+3600));
    }

    $(document).ready(function() {
        if ($('#global-loader').is(':visible') && $('#global-loader').css('display') !== 'none') {
            $('body').css('overflow', 'hidden');
            runCycle();
        }
    });

    $(window).on('load', function() {
        if ($('#global-loader').is(':visible')) {
            window.hideLoader();
        }
    });

    // Fix: When user presses Back, browser restores from bfcache.
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.hideLoader();
        }
    });

    window.showLoader = function(text = 'Loading') {
        $('#global-loader .loading-word').text(text);
        $('#global-loader').fadeIn(300, function() {
            stopCycle();
            runCycle();
        });
        $('body').css('overflow', 'hidden');
    }

    window.hideLoader = function() {
        $('#global-loader').fadeOut(400, function() {
            stopCycle();
        });
        $('body').css('overflow', '');
    }

    // Show loader on link clicks for smoother transitions
    $(document).on('click', 'a', function(e) {
        const href = $(this).attr('href');
        const target = $(this).attr('target');
        const isDownload = $(this).attr('download') !== undefined;
        
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript') || 
            target === '_blank' || href.startsWith('mailto') || href.startsWith('tel') || isDownload) {
            return;
        }

        if (e.which === 1 && !e.ctrlKey && !e.shiftKey && !e.altKey && !e.metaKey) {
            window.showLoader('Loading');
        }
    });

    $(window).on('beforeunload', function() {
        window.showLoader('Loading');
    });
</script>
