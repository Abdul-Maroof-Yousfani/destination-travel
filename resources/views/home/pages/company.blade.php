@extends('home/layouts/master')
@section('title', 'Home')
@section('style')
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;line-height:1.6;color:#333;}
        .container{max-width:1200px;margin:0 auto;padding:0 20px;}
        /* Hero Section */
        .hero-section{background:#0f7d9e;padding:80px 0;text-align:center;}
        .hero-text h1{font-size:3rem;font-weight:700;margin-bottom:20px;color:#fff;}
        /* Responsive */
        @media(max-width:768px){
 
        }
    </style>
@endsection
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Company <span class="highlight">Info</span></h1>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
@endsection