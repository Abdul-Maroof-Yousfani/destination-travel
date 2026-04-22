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
        .contact-section{padding:50px 0px;background:#e5e5e5;}
        .contact-info h2{font-size:30px;font-weight:600;margin-bottom:5px;}
        .contact-info h4{font-size:20px;font-weight:600;margin-bottom:10px;}
        .contact-info p{margin-bottom:30px;}
        .contact-info span{color:#127f9f;font-size:20px;margin-bottom:13px;text-transform:uppercase;}
        .contact-info h3{color:#000;font-size:20px;margin-bottom:20px;}
        .contact-info a h3 i{color:#127f9f;font-size:25px;margin-bottom:13px;}
        .contact-info h3 i{color:#127f9f;}
        .map-section{padding:30px 0px;}
        .map{flex:1;}
        .map iframe{width:100%;height:400px;border:0;}
        .form-box{flex:1;}
        .form-box h2{color:#1e2a78;margin-bottom:10px;}
        .form-box p{color:#666;margin-bottom:20px;}
        .form-group{margin-bottom:15px;}
        .form-group label{display:block;margin-bottom:5px;font-size:14px;}
        .form-group input,.form-group textarea{width:100%;padding:12px;border-radius:6px;border:1px solid #ddd;background:#f1f1f1;outline:none;}
        .form-group textarea{resize:none;height:100px;}
        .submit-btn{background:#0d7a8c;color:#fff;border:1px solid #0d7a8c;padding:12px 25px;border-radius:8px;cursor:pointer;font-weight:bold;}
        .submit-btn:hover{background:transparent;border:1px solid #0d7a8c;color:#0d7a8c;transition:0.3s;}
        /* Responsive */
        @media(max-width:768px){.main-box{flex-direction:column;}
        .contact-info h2{font-size:28px;}
        .contact-info{text-align:center;}
        .contact-info h3{color:#000;font-size:15px;margin-bottom:20px;}
        .form-box button{text-align:center;width:100%;}
        }
    </style>
@endsection
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Contact <span class="highlight">Us</span></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <!-- Main Section -->
            <div class="row align-items-center">
                <div class="col-md-12 col-lg-6">
                    <div class="contact-info">
                        <h2>GET IN TOUCH</h2>
                        <h4>FOR MORE INFORMATION</h4>
                        <p>We’re here to help. Contact us for expert guidance and assistance</p>
                        <a href="tel:{{ config('variables.contact.phone') }}"><h3><i class="fa-solid fa-phone"></i> {{ config('variables.contact.phone') }}</h3></a>
                        <a href="https://wa.me/{{ config('variables.contact.phone') }}" target="_blank"><span>whatsapp 24/7</span><h3><i class="fa-brands fa-whatsapp"></i> {{ config('variables.contact.phone') }}</h3></a>
                        <a href="mailto:support@edestination.com"><h3><i class="fa-solid fa-envelope"></i> support@edestination.com</h3></a>
                        <h3><i class="fa-solid fa-map-marker"></i> Office No. 1113, Plot 111, KS Trade Tower, Shahrah-e-Liaquat, New Chali Road, Karachi, Pakistan</h3>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <!-- Form -->
                    <div class="form-box">
                        <form action="submit.php" method="POST">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="full_name" placeholder="Full Name" required>
                            </div>
                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" name="email" placeholder="Email Address" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="tel" name="phone" placeholder="Phone Number" required>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" placeholder="Message"></textarea>
                            </div>
                            <button type="submit" class="submit-btn">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section">
        <div class="container">
            <!-- Main Section -->
            <div class="main-box2">
                <!-- Map -->
                <div class="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4331.994127619381!2d67.01386797595231!3d24.85673204540558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33e0eff26c7e5%3A0x5e1a4ac700449659!2sShahrah-e-Liaquat%2C%20Karachi%2C%20Pakistan!5e1!3m2!1sen!2s!4v1775547799562!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
@endsection