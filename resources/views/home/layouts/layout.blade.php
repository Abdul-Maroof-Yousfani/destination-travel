{{-- Use if you want to show data without sidebars --}}

@extends('home/layouts/master')

@include('home/layouts/navbar')

<!-- Content -->
@yield('content')
<!-- Content -->

@include('home/layouts/footer')
@endsection