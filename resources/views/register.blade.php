@extends('home/layouts/layout')

@section('title', 'Register')
@section('style')
  <link rel="stylesheet" href="{{ url('assets/css/login.css') }}">
@endsection
@section('content')
    <div class="container d-flex flex-column gap-3 m-auto w-50">
        <h1 class="headline">Register</h1>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter your Fullname">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your Email">
        </div>

        <div class="form-group">
            <label for="code">Phone Code</label>
            <input type="text" id="code" name="code" class="form-control" placeholder="Enter your Phone Code">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="number" id="phone" name="phone" class="form-control" placeholder="Enter your Phone">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your Password">
        </div>

        <button class="loginBtn">Register</button>
        <p class="registerText">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('.loginBtn').on('click', function (e) {
                e.preventDefault();
                let name = $('#name').val().trim();
                let email = $('#email').val().trim();
                let code = $('#code').val().trim();
                let phone = $('#phone').val().trim();
                let password = $('#password').val().trim();
                // console.log(url)
                // return

                // if (!name || !email || !code || !phone || !password) {
                //     _alert('Both fields are required.', 'error');
                //     return;
                // }

                $.ajax({
                    url: "{{ route('register.submit') }}",
                    method: 'POST',
                    data: {
                        email, password, name, code, phone,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: res => window.location.href = res.redirect,
                    error: function (xhr) {
                        $('.invalid-feedback').remove();
                        $('.form-control').removeClass('is-invalid');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            for (let field in errors) {
                                let messages = errors[field];
                                let fieldName = field.replace(/\.\d+/g, '[]');
                                let input = $(`[name="${fieldName}"]`);

                                if (input.length > 1 || fieldName.endsWith('[]')) {
                                    input.each(function () {
                                        $(this).addClass('is-invalid');
                                        $(this).after(`<div class="invalid-feedback d-block text-danger">${messages[0]}</div>`);
                                    });
                                } else {
                                    input.addClass('is-invalid');
                                    input.after(`<div class="invalid-feedback d-block text-danger">${messages[0]}</div>`);
                                }
                            }

                            // Scroll to first error
                            const firstInvalid = $('.is-invalid').first();
                            if (firstInvalid.length) {
                                $('html, body').animate({
                                    scrollTop: firstInvalid.offset().top - 100
                                }, 500);
                            }
                        } else {
                            _alert(xhr.responseJSON?.message || 'Registration failed.', 'error');
                            console.error(xhr.responseJSON);
                        }
                    }
                });
            });
        });
    </script>
@endsection