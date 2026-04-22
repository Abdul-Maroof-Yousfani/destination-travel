<div class="col-md-12 col-lg-3 br-left">
    <div class="support-box">
        <h4>24/7 Customer Support</h4>
        <div class="support-item">
            <a href="tel:{{ config('variables.contact.phone') }}"> <i class="fa fa-phone"></i>
            <span>{{ config('variables.contact.phone') }}</span></a>
        </div>
        <div class="support-item">
            <a href="tel:{{ config('variables.contact.phone') }}"> <i class="fa fa-mobile"></i>
            <span>{{ config('variables.contact.phone') }}</span></a>
        </div>
        <div class="support-item">
            <a href="mailto:support@edestination.com"> <i class="fa fa-envelope"></i>
            <span>support@edestination.com</span></a>
        </div>
    </div>
    <button onclick="sharePage()" class="btn btn-share  mt-2 "><i class="fa-regular fa-share-from-square"></i> Share</button>
</div>

<script>
    function sharePage() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: window.location.href
            }).then(() => {
                console.log('Thanks for sharing!');
            }).catch((err) => {
                console.error('Error sharing:', err);
            });
        } else {
            // Fallback for browsers that don't support Web Share API
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            }).catch((err) => {
                console.error('Failed to copy:', err);
                alert('Failed to copy link. Please copy manually.');
            });
        }
    }
</script>