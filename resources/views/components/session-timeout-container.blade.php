{{-- @dd($expTime); --}}
@if (isset($expTime) && $expTime)
<style>
    .footerTimeOutContainer{z-index:9999;position:fixed;bottom:0;left:0;right:0;background-color:#127f9fe0;color:#fff;padding:15px;transition:opacity 0.5s ease-in-out;}
</style>
<div class="footerTimeOutContainer">
    <div class="text-center idExpIn">
        <h3></h3>
    </div>
</div>
<script>
    let skipAncis;
    let countdown;
    const sessionTimer = action => {
        if (!action) {
            clearInterval(countdown);
            $(".idExpIn").text('');
            return;
        };

        let sessionEndTime = @json($expTime) || 0;
        let sessionEndTimestamp = new Date(sessionEndTime).getTime();

        if (!sessionEndTime) {
            $(".idExpIn h3").text("Invalid session end time");
            return;
        }

        function updateTimer() {
            let currentTime = new Date().getTime();
            let timeLeft = sessionEndTimestamp - currentTime;

            if (timeLeft <= 0) {
                skipAncis = true;
                $(".idExpIn h3").text("Session Expired");
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Your session has expired. Please go back and refresh.',
                    icon: 'warning',
                    confirmButtonText: 'Go Back',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    preConfirm: () => {
                        let goBack = localStorage.getItem('flights') || null;
                        goBack ? window.location.href = `/flights${goBack}` : window.history.back();
                    }
                });

                clearInterval(countdown);
                return;
            }

            let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            let formattedMinutes = minutes < 10 ? `0${minutes} Minutes` : `${minutes} Minutes`;
            let formattedSeconds = seconds < 10 ? `0${seconds} Seconds` : `${seconds} Seconds`;

            $(".idExpIn h3").html(`Please finish your booking in : <span class="font-weight-bolder">${formattedMinutes}, ${formattedSeconds}</span>`);
        }

        if (new Date().getTime() >= sessionEndTimestamp) {
            skipAncis = true;
            $(".idExpIn h3").text("Session Expired");
            Swal.fire({
                title: 'Session Expired',
                text: 'Your session has expired. Please go back and refresh.',
                icon: 'warning',
                confirmButtonText: 'Go Back',
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: () => {
                    let goBack = localStorage.getItem('flights') || null;
                    goBack ? window.location.href = `/flights${goBack}` : window.history.back();
                }
            });
            return;
        } else {
            updateTimer();
            countdown = setInterval(updateTimer, 1000);
        }
    }
    sessionTimer(true);
</script>
@endif