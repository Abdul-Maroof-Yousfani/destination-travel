<script>
    $(document).ready(function () {
        $('.logoutBtn').on('click', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.logout') }}",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    window.location.href = "{{ route('admin.login') }}";
                },
                error: function (xhr) {
                    alert('Logout failed.');
                }
            });
        });
    });
</script>
