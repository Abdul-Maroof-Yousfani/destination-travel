@extends('home/layouts/master')

@section('title', 'Home')
@section('style')
{{-- style --}}
@endsection
@section('content')
<style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Inter',sans-serif;line-height:1.6;color:#333;}
    .container{max-width:1200px;margin:0 auto;padding:0 20px;}
    .search-bookings{padding:80px 0;background:#f8fafc;}
    .booking-box{background:#fff;padding:40px 50px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.08);width:400px;text-align:center;}
    .booking-box h2{font-size:22px;font-weight:600;margin-bottom:30px;color:#222;}
    .form-group{text-align:left;margin-bottom:20px;}
    .form-group label{font-size:14px;font-weight:600;display:block;margin-bottom:6px;color:#333;}
    .form-group input{width:100%;padding:10px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;outline:none;transition:border-color 0.2s ease;}
    .form-group input:focus{border-color:#666;}
    .form-note{font-size:12px;color:#666;margin-top:4px;}
    button{width:100%;padding:12px;font-size:16px;background:#e5e5e5;border:none;border-radius:4px;cursor:not-allowed;color:#666;}
    button.active{background:#000;color:#fff;cursor:pointer;}
</style>

    <section class="search-bookings">
        <div class="container">
            <div class="row">
                <div class="col-md-3"> </div>
                <div class="col-md-6">
                    <div class="booking-box">
                        <h2>Search for your bookings</h2>
                        <form>
                          <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" placeholder="4546385" required>
                            <div class="form-note">Your Order ID is emailed with booking confirmation.</div>
                          </div>
                    
                          <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" placeholder="e.g. name@gmail.com" required>
                            <div class="form-note">The email address entered during booking.</div>
                          </div>
                          <button type="submit">Search</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-3"></div>
           </div>
        </div>
    </section>

@endsection
@section('script')
@endsection