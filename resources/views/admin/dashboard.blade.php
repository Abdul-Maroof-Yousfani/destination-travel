@extends('admin/layouts/master')

@section('title', 'Home')
@section('style')
{{-- style --}}
@endsection
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-12">
         @auth
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>You are logged in as an {{ auth()->user()->getRoleNames()->first() }}</p>
         @endauth
         <p>This is the admin dashboard where you can manage your application.</p>
      </div>
   </div>
</div>
@endsection
@section('script')
{{-- script --}}
@endsection