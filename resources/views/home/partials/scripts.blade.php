<script src="{{ url('assets/js/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	new WOW().init();
	const _alert = (msg, type = 'success') => {
        const icons = {
            success: '#28a745',
            error: '#f27474',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        Swal.fire({
            position: 'top-end',
            icon: type,
            title: `<span style="font-size: 15px;">${msg}</span>`,
            showConfirmButton: false,
            timer: 2000,
            toast: true,
            background: '#fff',
            customClass: { title: 'custom-title', popup: 'custom-toast' },
            iconColor: icons[type] || icons.success
        });
    }
</script>