@props(['showOnLoad' => false])

<div id="global-loader" style="{{ $showOnLoad ? 'display: block;' : 'display: none;' }}">
    <div class="loader-overlay">
        <div class="loader-content">
            <div class="spinner-container">
                <div class="main-spinner"></div>
                <div class="pulse-ring"></div>
            </div>
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
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loader-content {
        text-align: center;
    }

    .spinner-container {
        position: relative;
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
    }

    .main-spinner {
        width: 100%;
        height: 100%;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #00788a;
        border-radius: 50%;
        animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
    }

    .pulse-ring {
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        border: 2px solid rgba(0, 120, 138, 0.2);
        border-radius: 50%;
        animation: pulse 1.5s ease-out infinite;
    }

    .loader-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #1a1a1a;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
    }

    .loading-dots span {
        animation: dots 1.4s infinite both;
    }

    .loading-dots span:nth-child(2) { animation-delay: 0.2s; }
    .loading-dots span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0% { transform: scale(0.8); opacity: 0; }
        50% { opacity: 0.5; }
        100% { transform: scale(1.2); opacity: 0; }
    }

    @keyframes dots {
        0%, 80%, 100% { opacity: 0; }
        40% { opacity: 1; }
    }
</style>

<script>
    $(document).ready(function() {
        if ($('#global-loader').is(':visible')) {
            $('body').css('overflow', 'hidden');
        }
    });

    $(window).on('load', function() {
        if ($('#global-loader').is(':visible')) {
            window.hideLoader();
        }
    });

    // Fix: When user presses Back, browser restores from bfcache.
    // The 'load' event does NOT re-fire in bfcache — so we must listen
    // to 'pageshow' with persisted=true to hide any lingering loader.
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.hideLoader();
        }
    });

    window.showLoader = function(text = 'Loading') {
        $('#global-loader .loading-word').text(text);
        $('#global-loader').fadeIn(300);
        $('body').css('overflow', 'hidden');
    }

    window.hideLoader = function() {
        $('#global-loader').fadeOut(300);
        $('body').css('overflow', '');
    }

    // Show loader on link clicks for smoother transitions
    $(document).on('click', 'a', function(e) {
        const href = $(this).attr('href');
        const target = $(this).attr('target');
        const isDownload = $(this).attr('download') !== undefined;
        
        // Skip if:
        // - No href or empty
        // - Hash link
        // - Javascript link
        // - target="_blank"
        // - mailto/tel
        // - download link
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript') || 
            target === '_blank' || href.startsWith('mailto') || href.startsWith('tel') || isDownload) {
            return;
        }

        // Only show if it's a left click and no modifier keys
        if (e.which === 1 && !e.ctrlKey && !e.shiftKey && !e.altKey && !e.metaKey) {
            window.showLoader('Loading');
        }
    });

    $(window).on('beforeunload', function() {
        window.showLoader('Loading');
    });
</script>
