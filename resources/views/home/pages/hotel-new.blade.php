<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edestinations - Discover Luxury Stays Around the World</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --teal: #0a9688;
      --teal-dark: #007a6e;
      --teal-light: #e0f4f2;
      --dark: #1a2940;
      --dark2: #243045;
      --gray: #6b7280;
      --light: #f7f8fa;
      --white: #ffffff;
      --yellow: #f5a623;
    }

    body { font-family: 'Poppins', sans-serif; color: #333; background: #fff; }

    /* ===== NAVBAR ===== */
    .navbar {
      background: var(--dark);
      padding: 10px 0;
      position: sticky;
      top: 0;
      z-index: 999;
    }
    .navbar .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      color: white;
      text-decoration: none;
    }
    .logo-icon {
      width: 36px;
      height: 36px;
      background: var(--teal);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: white;
    }
    .logo span { font-size: 15px; font-weight: 700; color: white; }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 5px;
      list-style: none;
    }
    .nav-links a {
      color: #ccc;
      text-decoration: none;
      font-size: 13px;
      padding: 5px 10px;
      border-radius: 4px;
      transition: color 0.2s;
    }
    .nav-links a:hover { color: white; }
    .nav-links a.active {
      background: var(--teal);
      color: white;
      border-radius: 20px;
      padding: 4px 14px;
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .nav-phone { color: #ccc; font-size: 12px; }
    .nav-phone i { color: var(--teal); margin-right: 4px; }
    .btn-login {
      background: transparent;
      border: 1px solid #ccc;
      color: #ccc;
      padding: 5px 16px;
      border-radius: 20px;
      font-size: 13px;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
    }
    .btn-signup {
      background: var(--teal);
      border: none;
      color: white;
      padding: 6px 18px;
      border-radius: 20px;
      font-size: 13px;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
    }
    .btn-signup:hover { background: var(--teal-dark); }

    /* ===== HERO ===== */
    .hero {
      background: linear-gradient(rgba(10,30,50,0.65), rgba(10,30,50,0.65)),
        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400&q=80') center/cover no-repeat;
      padding: 80px 20px 100px;
      text-align: center;
      color: white;
      min-height: 480px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .hero h1 {
      font-size: 42px;
      font-weight: 800;
      line-height: 1.2;
      margin-bottom: 14px;
      text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .hero p {
      font-size: 14px;
      color: #d0e8e6;
      margin-bottom: 36px;
      max-width: 500px;
    }

    /* Search Box */
    .search-box {
      background: white;
      border-radius: 12px;
      padding: 24px 28px 20px;
      max-width: 820px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(0,0,0,0.25);
    }
    .search-box label {
      display: block;
      font-size: 11px;
      color: #888;
      font-weight: 500;
      margin-bottom: 4px;
    }
    .search-destination {
      margin-bottom: 16px;
      text-align: left;
    }
    .search-destination input {
      width: 100%;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 10px 14px 10px 36px;
      font-size: 13px;
      font-family: 'Poppins', sans-serif;
      outline: none;
      color: #333;
      background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23888' viewBox='0 0 16 16'%3E%3Cpath d='M8 1a5 5 0 1 0 0 10A5 5 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z'/%3E%3C/svg%3E") no-repeat 12px center;
    }

    .search-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 14px;
      margin-bottom: 18px;
      text-align: left;
    }
    .search-field { position: relative; }
    .search-field input {
      width: 100%;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 10px 14px 10px 36px;
      font-size: 13px;
      font-family: 'Poppins', sans-serif;
      outline: none;
      color: #555;
    }
    .search-field .field-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--teal);
      font-size: 13px;
    }

    .btn-search {
      background: var(--teal);
      color: white;
      border: none;
      padding: 12px 32px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.2s;
    }
    .btn-search:hover { background: var(--teal-dark); }

    /* ===== STATS ===== */
    .stats {
      background: white;
      padding: 40px 20px;
      border-bottom: 1px solid #f0f0f0;
    }
    .stats .container {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      text-align: center;
    }
    .stat-item { padding: 10px; }
    .stat-icon {
      width: 52px;
      height: 52px;
      background: var(--teal-light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
      color: var(--teal);
      font-size: 20px;
    }
    .stat-item h3 { font-size: 26px; font-weight: 800; color: #222; }
    .stat-item p { font-size: 12px; color: #888; margin-top: 2px; }

    /* ===== DESTINATIONS ===== */
    .destinations {
      background: var(--light);
      padding: 60px 20px;
      text-align: center;
    }
    .section-title { font-size: 28px; font-weight: 700; color: #1a2940; margin-bottom: 6px; }
    .section-sub { font-size: 13px; color: #888; margin-bottom: 36px; }

    .dest-grid {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }
    .dest-card {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      height: 200px;
      cursor: pointer;
    }
    .dest-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s;
    }
    .dest-card:hover img { transform: scale(1.05); }
    .dest-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);
    }
    .dest-info {
      position: absolute;
      bottom: 12px;
      left: 14px;
      color: white;
    }
    .dest-info h4 { font-size: 16px; font-weight: 700; }
    .dest-tag {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(0,0,0,0.5);
      color: white;
      font-size: 10px;
      padding: 3px 8px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .dest-duration {
      position: absolute;
      top: 10px;
      right: 10px;
      background: var(--teal);
      color: white;
      font-size: 10px;
      padding: 3px 8px;
      border-radius: 20px;
    }

    /* ===== WHY US ===== */
    .why-us {
      padding: 60px 20px;
      text-align: center;
      background: white;
    }
    .why-grid {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 20px;
      margin-top: 36px;
    }
    .why-card { padding: 16px 10px; }
    .why-icon {
      width: 56px;
      height: 56px;
      background: var(--teal-light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
      color: var(--teal);
      font-size: 22px;
    }
    .why-card h4 { font-size: 13px; font-weight: 700; color: #222; margin-bottom: 6px; }
    .why-card p { font-size: 11px; color: #888; line-height: 1.5; }

    /* ===== TESTIMONIALS ===== */
    .testimonials {
      background: var(--light);
      padding: 60px 20px;
      text-align: center;
    }
    .testi-grid {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
      margin-top: 36px;
    }
    .testi-card {
      background: white;
      border-radius: 12px;
      padding: 22px;
      text-align: left;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .testi-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 10px;
    }
    .testi-avatar {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      object-fit: cover;
    }
    .testi-name { font-size: 13px; font-weight: 700; color: #222; }
    .testi-role { font-size: 11px; color: #aaa; }
    .stars { color: var(--yellow); font-size: 12px; margin-bottom: 8px; }
    .testi-text { font-size: 12px; color: #666; line-height: 1.7; }

    /* ===== FOOTER ===== */
    footer {
      background: var(--dark);
      color: #aaa;
      padding: 50px 20px 20px;
    }
    .footer-container {
      max-width: 1100px;
      margin: 0 auto;
    }
    .footer-top {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
      gap: 30px;
      margin-bottom: 40px;
    }
    .footer-about p {
      font-size: 12px;
      line-height: 1.8;
      margin: 12px 0 16px;
      color: #888;
    }
    .footer-contact { font-size: 12px; color: #888; }
    .footer-contact p { margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px; }
    .footer-contact i { color: var(--teal); margin-top: 2px; }

    .footer-col h5 {
      color: white;
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 16px;
    }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: 8px; }
    .footer-col ul li a { color: #888; text-decoration: none; font-size: 12px; transition: color 0.2s; }
    .footer-col ul li a:hover { color: var(--teal); }

    .footer-newsletter {
      border-top: 1px solid #2d3a4f;
      padding-top: 24px;
      margin-top: 10px;
      text-align: center;
    }
    .footer-newsletter h5 { color: white; font-size: 15px; margin-bottom: 6px; }
    .footer-newsletter p { font-size: 12px; color: #777; margin-bottom: 16px; }
    .newsletter-form {
      display: flex;
      max-width: 440px;
      margin: 0 auto;
      gap: 0;
    }
    .newsletter-form input {
      flex: 1;
      padding: 10px 16px;
      border: none;
      border-radius: 8px 0 0 8px;
      font-size: 13px;
      font-family: 'Poppins', sans-serif;
      outline: none;
    }
    .newsletter-form button {
      background: var(--teal);
      color: white;
      border: none;
      padding: 10px 22px;
      border-radius: 0 8px 8px 0;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
    }

    .footer-bottom {
      border-top: 1px solid #2d3a4f;
      padding-top: 20px;
      margin-top: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .social-links { display: flex; gap: 10px; }
    .social-links a {
      width: 32px;
      height: 32px;
      background: #2d3a4f;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #aaa;
      font-size: 13px;
      text-decoration: none;
      transition: background 0.2s;
    }
    .social-links a:hover { background: var(--teal); color: white; }
    .copyright { font-size: 12px; color: #666; }

    /* Container utility */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
  </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
  <div class="container">
    <a href="#" class="logo">
      <div class="logo-icon"><i class="fas fa-globe"></i></div>
      <span>eDestinations</span>
    </a>
    <ul class="nav-links">
      <li><a href="#">Flights</a></li>
      <li><a href="#" class="active">Hotels</a></li>
      <li><a href="#">Dinner</a></li>
      <li><a href="#">Tour</a></li>
      <li><a href="#">Cruises</a></li>
      <li><a href="#">My Bookings</a></li>
    </ul>
    <div class="nav-right">
      <span class="nav-phone"><i class="fas fa-phone"></i> +1 (800) 123-4567</span>
      <button class="btn-login">Login</button>
      <button class="btn-signup">Sign Up</button>
    </div>
  </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
  <h1>Discover Luxury Stays<br>Around the World</h1>
  <p>Experience premium hospitality with exclusive deals on hotels, flights, and travel packages.</p>

  <div class="search-box">
    <div class="search-destination">
      <label>Destination</label>
      <input type="text" placeholder="Dubai" />
    </div>
    <div class="search-row">
      <div class="search-field">
        <label>Check in</label>
        <span class="field-icon"><i class="fas fa-calendar"></i></span>
        <input type="text" placeholder="MM/DD/YYYY" />
      </div>
      <div class="search-field">
        <label>Check out</label>
        <span class="field-icon"><i class="fas fa-calendar"></i></span>
        <input type="text" placeholder="MM/DD/YYYY" />
      </div>
      <div class="search-field">
        <label>Guests &amp; Rooms</label>
        <span class="field-icon"><i class="fas fa-user"></i></span>
        <input type="text" placeholder="1 Guest(s), 1 Room" />
      </div>
    </div>
    <button class="btn-search"><i class="fas fa-search"></i> Search Hotels</button>
  </div>
</section>

<!-- ===== STATS ===== -->
<section class="stats">
  <div class="container">
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-users"></i></div>
      <h3>50K+</h3>
      <p>Happy Travelers</p>
    </div>
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-hotel"></i></div>
      <h3>5,000+</h3>
      <p>Active Hotel Listing</p>
    </div>
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-tag"></i></div>
      <h3>100%</h3>
      <p>Best Price Guarantee</p>
    </div>
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-headset"></i></div>
      <h3>24/7</h3>
      <p>Customer Support</p>
    </div>
  </div>
</section>

<!-- ===== DESTINATIONS ===== -->
<section class="destinations">
  <h2 class="section-title">Explore Popular Destinations</h2>
  <p class="section-sub">Discover the world's most sought-after travel destinations</p>

  <div class="dest-grid">

    <!-- Dubai -->
    <div class="dest-card">
      <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80" alt="Dubai"/>
      <div class="dest-overlay"></div>
      <span class="dest-tag"><i class="fas fa-star"></i> 4.9</span>
      <span class="dest-duration">230 Hotels</span>
      <div class="dest-info"><h4>Dubai</h4></div>
    </div>

    <!-- Istanbul -->
    <div class="dest-card">
      <img src="https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=600&q=80" alt="Istanbul"/>
      <div class="dest-overlay"></div>
      <span class="dest-tag"><i class="fas fa-star"></i> 4.8</span>
      <span class="dest-duration">175 Hotels</span>
      <div class="dest-info"><h4>Istanbul</h4></div>
    </div>

    <!-- Bangkok -->
    <div class="dest-card">
      <img src="https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=600&q=80" alt="Bangkok"/>
      <div class="dest-overlay"></div>
      <span class="dest-tag"><i class="fas fa-star"></i> 4.7</span>
      <span class="dest-duration">210 Hotels</span>
      <div class="dest-info"><h4>Bangkok</h4></div>
    </div>

    <!-- Kuala Lumpur -->
    <div class="dest-card">
      <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=600&q=80" alt="Kuala Lumpur"/>
      <div class="dest-overlay"></div>
      <span class="dest-tag"><i class="fas fa-star"></i> 4.6</span>
      <span class="dest-duration">190 Hotels</span>
      <div class="dest-info"><h4>Kuala Lumpur</h4></div>
    </div>

    <!-- Karachi -->
    <div class="dest-card">
      <img src="https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=600&q=80" alt="Karachi"/>
      <div class="dest-overlay"></div>
      <span class="dest-tag"><i class="fas fa-star"></i> 4.5</span>
      <span class="dest-duration">95 Hotels</span>
      <div class="dest-info"><h4>Karachi</h4></div>
    </div>

    <!-- Makkah -->
    <div class="dest-card">
      <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=600&q=80" alt="Makkah"/>
      <div class="dest-overlay"></div>
      <span class="dest-tag"><i class="fas fa-star"></i> 5.0</span>
      <span class="dest-duration">120 Hotels</span>
      <div class="dest-info"><h4>Makkah</h4></div>
    </div>

  </div>
</section>

<!-- ===== WHY CHOOSE US ===== -->
<section class="why-us">
  <h2 class="section-title">Why Choose Us</h2>
  <p class="section-sub">Experience the difference with our premium travel services</p>

  <div class="why-grid">
    <div class="why-card">
      <div class="why-icon"><i class="fas fa-bolt"></i></div>
      <h4>Easy Booking</h4>
      <p>Simple and quick booking process with secured payment.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fas fa-lock"></i></div>
      <h4>Secure Payments</h4>
      <p>Bank-level encryption and fraud prevention protection.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fas fa-globe"></i></div>
      <h4>Global Hotels</h4>
      <p>Access to 5,000+ premium hotels in 200 destinations worldwide.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fas fa-check-circle"></i></div>
      <h4>Instant Confirmation</h4>
      <p>Get instantly booking confirmation via email and SMS.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fas fa-headset"></i></div>
      <h4>24/7 Support</h4>
      <p>Round-the-clock customer support in multiple languages.</p>
    </div>
  </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="testimonials">
  <h2 class="section-title">What Our Travelers Say</h2>
  <p class="section-sub">Real experiences from our valued customers</p>

  <div class="testi-grid">
    <div class="testi-card">
      <div class="testi-header">
        <img class="testi-avatar" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Johnson"/>
        <div>
          <div class="testi-name">Sarah Johnson</div>
          <div class="testi-role">New York</div>
        </div>
      </div>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
      <p class="testi-text">Absolutely stunning experience! The booking process was so simple and the hotel recommendations were perfect. Will definitely use eDestinations again for my next trip.</p>
    </div>

    <div class="testi-card">
      <div class="testi-header">
        <img class="testi-avatar" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Ahmed Al-Rashid"/>
        <div>
          <div class="testi-name">Ahmed Al-Rashid</div>
          <div class="testi-role">Dubai</div>
        </div>
      </div>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
      <p class="testi-text">The eDestinations platform has been used for countless trips. The customer service is outstanding and the prices are always unbeatable. Highly recommended!</p>
    </div>

    <div class="testi-card">
      <div class="testi-header">
        <img class="testi-avatar" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Emma Thompson"/>
        <div>
          <div class="testi-name">Emma Thompson</div>
          <div class="testi-role">London</div>
        </div>
      </div>
      <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
      <p class="testi-text">From booking to checkout, everything was seamless. The luxury hotels were exactly as described. This is my go-to travel platform from now on!</p>
    </div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
  <div class="footer-container">
    <div class="footer-top">

      <!-- About -->
      <div class="footer-about">
        <a href="#" class="logo" style="display:inline-flex; margin-bottom: 4px;">
          <div class="logo-icon" style="background: var(--teal);"><i class="fas fa-globe"></i></div>
          <span style="color: white; margin-left: 8px;">eDestinations</span>
        </a>
        <p>Your trusted partner for luxury travel experiences worldwide. We connect you with premium accommodations and unique travel experiences.</p>
        <div class="footer-contact">
          <p><i class="fas fa-map-marker-alt"></i> 127 Street West, New York, NY 10003</p>
          <p><i class="fas fa-phone"></i> +1 (800) 123-4567</p>
          <p><i class="fas fa-envelope"></i> info@edestinations.com</p>
        </div>
      </div>

      <!-- Company -->
      <div class="footer-col">
        <h5>Company</h5>
        <ul>
          <li><a href="#">About</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Press</a></li>
          <li><a href="#">Blog</a></li>
          <li><a href="#">Partnerships</a></li>
        </ul>
      </div>

      <!-- Support -->
      <div class="footer-col">
        <h5>Support</h5>
        <ul>
          <li><a href="#">Help Center</a></li>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div class="footer-col">
        <h5>Quick Links</h5>
        <ul>
          <li><a href="#">Hotels</a></li>
          <li><a href="#">Flights</a></li>
          <li><a href="#">Holiday Packages</a></li>
          <li><a href="#">Cruises</a></li>
          <li><a href="#">Car Rentals</a></li>
        </ul>
      </div>

      <!-- Destinations -->
      <div class="footer-col">
        <h5>Destinations</h5>
        <ul>
          <li><a href="#">Dubai</a></li>
          <li><a href="#">Istanbul</a></li>
          <li><a href="#">Bangkok</a></li>
          <li><a href="#">Makkah</a></li>
          <li><a href="#">Karachi</a></li>
        </ul>
      </div>
    </div>

    <!-- Newsletter -->
    <div class="footer-newsletter">
      <h5>Subscribe to Our Newsletter</h5>
      <p>Stay updated with the best travel deals delivered to your inbox</p>
      <div class="newsletter-form">
        <input type="email" placeholder="Enter your email" />
        <button>Subscribe</button>
      </div>
    </div>

    <!-- Bottom -->
    <div class="footer-bottom">
      <div class="social-links">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
      <p class="copyright">&copy; 2025 eDestinations. All rights reserved.</p>
    </div>
  </div>
</footer>

</body>
</html>