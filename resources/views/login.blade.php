@extends('home/layouts/layout')

@section('title', 'Login')
@section('style')
  <link rel="stylesheet" href="{{ url('assets/css/login.css') }}">
@endsection
@section('content')
    @php $isAdmin = request()->routeIs('admin.login') ? true : false; @endphp
    <div class="container d-flex flex-column gap-3 m-auto w-50">
        <p class="headline">{{ $isAdmin ? 'Admin' : '' }} Login</p>

        <div class="form-group">
            <label for="name">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your Email">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password">
        </div>

        <button class="loginBtn">Login</button>
        @if (!$isAdmin)
            <p class="registerText">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        @endif
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            const isAdmin = @json($isAdmin);
            $('.loginBtn').on('click', function (e) {
                e.preventDefault();
                let url = isAdmin ? "{{ route('admin.login.submit') }}" : "{{ route('login.submit') }}";
                let email = $('#email').val().trim();
                let password = $('#password').val().trim();
                // console.log(url)
                // return

                if (!email || !password) {
                    _alert('Both fields are required.', 'error');
                    return;
                }

                $.ajax({
                    url,
                    method: 'POST',
                    data: {
                        email: email,
                        password: password,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: res => {
                        let currentUrl = window.location.href;
                        if (currentUrl.includes('/admin') || currentUrl.includes('/agent')) {
                            window.location.href = res.redirect
                        } else {
                            let goBack = localStorage.getItem('flights') || null;
                            window.location.href = (goBack ? `/flights${goBack}` : res.redirect);
                        }
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Login failed.';
                        _alert(msg, 'error');
                    }
                });
            });
        });
    </script>
@endsection