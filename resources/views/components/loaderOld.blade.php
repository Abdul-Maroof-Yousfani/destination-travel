@props(['showOnLoad' => false])

<div id="global-loader" style="{{ $showOnLoad ? 'display: block;' : 'display: none;' }}">
    <div class="loader-overlay">
        
        <div class="cube-container">
            <div class="cube-x">
                <div class="cube-y">
                    <!-- Front (Image 1) -->
                    <div class="cube-face cube-front">
                        <img src="{{ asset('assets/images/loader/animation1.jpg') }}" alt="Loading 1" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80'">
                    </div>
                    <!-- Right (Image 2) -->
                    <div class="cube-face cube-right">
                        <img src="{{ asset('assets/images/loader/animation2.jpg') }}" alt="Loading 2" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1527838832700-5059252407fa?w=800&q=80'">
                    </div>
                    <!-- Back (Image 3) -->
                    <div class="cube-face cube-back">
                        <img src="{{ asset('assets/images/loader/animation3.jpg') }}" alt="Loading 3" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1496588152823-86ff7695e68f?w=800&q=80'">
                    </div>
                    <!-- Left (Image 4) -->
                    <div class="cube-face cube-left">
                        <img src="{{ asset('assets/images/loader/animation4.jpg') }}" alt="Loading 4" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80'">
                    </div>
                    <!-- Top (Collage) -->
                    <div class="cube-face cube-top">
                        <div class="collage-grid">
                            <img src="{{ asset('assets/images/loader/animation1.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&q=80'">
                            <img src="{{ asset('assets/images/loader/animation2.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1527838832700-5059252407fa?w=400&q=80'">
                            <img src="{{ asset('assets/images/loader/animation3.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1496588152823-86ff7695e68f?w=400&q=80'">
                            <img src="{{ asset('assets/images/loader/animation4.jpg') }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=400&q=80'">
                        </div>
                    </div>
                    <!-- Bottom -->
                    <div class="cube-face cube-bottom"></div>
                </div>
            </div>
        </div>

        <div class="loader-text-container">
            <div class="loader-text">
                <span class="loading-word">{{ $showOnLoad ? 'Loading' : 'Searching' }}</span>
                <div class="loading-dots">
                    <span>.</span><span>.</span><span>.</span>
                </div>
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
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 999999;
    }

    .loader-overlay {
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.634);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    /* 3D CUBE CONTAINER */
    .cube-container {
        width: var(--cube-size);
        height: var(--cube-size);
        perspective: 1200px;
        position: relative;
    }

    .cube-x {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transform: translateZ(calc(var(--half-size) * -1)) rotateX(0deg);
    }

    .cube-y {
        width: 100%;
        height: 100%;
        position: absolute;
        transform-style: preserve-3d;
    }

    .cube-face {
        position: absolute;
        width: var(--cube-size);
        height: var(--cube-size);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: inset 0 0 15px rgba(0,0,0,0.1), 0 10px 25px rgba(0,0,0,0.15);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        background: #fff;
    }

    .cube-face img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cube-front  { transform: rotateY(0deg) translateZ(var(--half-size)); }
    .cube-right  { transform: rotateY(90deg) translateZ(var(--half-size)); }
    .cube-back   { transform: rotateY(180deg) translateZ(var(--half-size)); }
    .cube-left   { transform: rotateY(-90deg) translateZ(var(--half-size)); }
    .cube-top    { transform: rotateX(90deg) translateZ(var(--half-size)); }
    .cube-bottom { transform: rotateX(-90deg) translateZ(var(--half-size)); background: #eee; }

    /* COLLAGE */
    .collage-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 2px;
        width: 100%;
        height: 100%;
        background: #fff;
    }

    /* CUBE ANIMATIONS */
    .cube-container.animate .cube-y { animation: spinCubeY 12s infinite ease-in-out; }
    .cube-container.animate .cube-x { animation: spinCubeX 12s infinite ease-in-out; }

    @keyframes spinCubeY {
        0%, 15% { transform: rotateY(0deg); }
        22%, 37% { transform: rotateY(-90deg); }
        44%, 59% { transform: rotateY(-180deg); }
        66%, 75% { transform: rotateY(-270deg); }
        85%, 100% { transform: rotateY(-360deg); }
    }

    @keyframes spinCubeX {
        0%, 75% { transform: translateZ(calc(var(--half-size) * -1)) rotateX(0deg); }
        85%, 95% { transform: translateZ(calc(var(--half-size) * -1)) rotateX(-90deg); }
        100% { transform: translateZ(calc(var(--half-size) * -1)) rotateX(0deg); }
    }

    /* Loader Text */
    .loader-text-container {
        margin-top: 40px;
        text-align: center;
        z-index: 10;
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
    }

    .loading-dots span {
        animation: dots 1.4s infinite both;
    }

    .loading-dots span:nth-child(2) { animation-delay: 0.2s; }
    .loading-dots span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes dots {
        0%, 80%, 100% { opacity: 0; }
        40% { opacity: 1; }
    }

    @media (max-width: 768px) {
        .loader-text {
            font-size: 16px;
        }
    }
</style>

<script>
    function resetFlipAnimation() {
        $('.cube-container').removeClass('animate');
        
        // Force reflow to restart animations cleanly
        if(document.querySelector('.cube-container')) {
            void document.querySelector('.cube-container').offsetWidth;
        }
    }

    function startFlipAnimation() {
        resetFlipAnimation();
        $('.cube-container').addClass('animate');
    }

    $(document).ready(function() {
        if ($('#global-loader').is(':visible') && $('#global-loader').css('display') !== 'none') {
            $('body').css('overflow', 'hidden');
            startFlipAnimation();
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
            startFlipAnimation();
        });
        $('body').css('overflow', 'hidden');
    }

    window.hideLoader = function() {
        $('#global-loader').fadeOut(400, function() {
            resetFlipAnimation();
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
