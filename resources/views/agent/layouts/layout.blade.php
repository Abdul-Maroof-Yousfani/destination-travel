{{-- Use if you want to show data without sidebars --}}

@extends('agent/layouts/master')

@include('agent/layouts/navbar')

<!-- Content -->
@yield('content')
<!-- Content -->

@include('agent/layouts/footer')
@endsection