@extends('home/layouts/master')

@section('title', 'Register')
@section('style')
    <link rel="stylesheet" href="{{ url('assets/css/login.css') }}">
@endsection
@section('content')
    <section class="search-bookings">
        <div class="container d-flex justify-content-center">
            <div class="booking-box">
                <h2>Create your account</h2>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" autocomplete="name" placeholder="Enter your Full Name" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" autocomplete="email" placeholder="Enter Your Email" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                </div>

                <div class="form-group">
                    <label for="code">Phone Code</label>
                    <input type="text" id="code" name="code" autocomplete="tel" placeholder="e.g. +1"oninput="this.value = this.value.replace(/[^0-9+]/g, '')" required> 
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <!-- <input type="number" id="phone" name="phone" placeholder="Enter Your Phone"> -->
                    <input type="tel" id="phone" name="phone" autocomplete="tel" placeholder="Enter Your Phone" inputmode="numeric" pattern="[0-9]*"oninput="this.value = this.value.replace(/[^0-9+]/g, '')" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" placeholder="Enter Your Password" required>
                </div>

                <button class="loginBtn active">Register</button>
                <p class="registerText mt-2">Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </section>
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

                // basic email regex
                let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!name || !email || !code || !phone || !password) {
                    return _alert('All fields are required.', 'warning');
                }

                if (!emailRegex.test(email)) {
                    return _alert('Please enter a valid email address.', 'warning');
                }

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
                                let input = $(`[name="${field}"]`);
                                input.addClass('is-invalid');
                                input.after(`<div class="invalid-feedback d-block text-danger">${messages[0]}</div>`);
                            }
                        } else {
                            _alert(xhr.responseJSON?.message || 'Registration failed.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endsection
