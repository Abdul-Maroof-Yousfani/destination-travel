<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Edestinations – Discover Luxury Stays Around the World</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Poppins',sans-serif;color:#222;overflow-x:hidden;}

/* ── NAV ── */
nav{display:flex;align-items:center;justify-content:space-between;padding:14px 60px;background:#fff;position:sticky;top:0;z-index:200;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.nav-logo{display:flex;align-items:center;gap:8px;text-decoration:none;}
.nav-logo-icon{width:36px;height:36px;background:linear-gradient(135deg,#0d7c6b,#1aad96);border-radius:8px;display:flex;align-items:center;justify-content:center;}
.nav-logo-icon i{color:#fff;font-size:18px;}
.nav-logo-text{font-size:18px;font-weight:800;color:#111;}
.nav-logo-text span{color:#0d7c6b;}
.nav-links{display:flex;align-items:center;gap:6px;list-style:none;}
.nav-links a{text-decoration:none;color:#555;font-size:13.5px;font-weight:500;padding:6px 14px;border-radius:20px;transition:.2s;}
.nav-links a:hover{color:#0d7c6b;}
.nav-links a.active{background:#0d7c6b;color:#fff !important;}
.nav-right{display:flex;align-items:center;gap:12px;}
.nav-phone{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#222;}
.nav-phone i{color:#0d7c6b;}
.btn-login{background:transparent;border:1.5px solid #0d7c6b;color:#0d7c6b;padding:7px 20px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;transition:.2s;font-family:'Poppins',sans-serif;}
.btn-login:hover{background:#e6f5f2;}
.btn-signup{background:#0d7c6b;color:#fff;border:none;padding:8px 20px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:.2s;}
.btn-signup:hover{background:#0b6b5c;}
.nav-hamburger{display:none;background:none;border:none;font-size:22px;color:#222;cursor:pointer;}
.nav-mobile-menu{display:none;flex-direction:column;gap:4px;background:#fff;padding:16px 20px;border-top:1px solid #f0f0f0;}
.nav-mobile-menu a{text-decoration:none;color:#555;font-size:14px;padding:8px 0;border-bottom:1px solid #f5f5f5;font-weight:500;}
.nav-mobile-menu a.active{color:#0d7c6b;font-weight:700;}

/* ── HERO ── */
.hero{position:relative;min-height:520px;display:flex;align-items:center;padding:70px 60px 60px;overflow:hidden;}
.hero-bg{position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1600&q=80') center/cover no-repeat;}
.hero-bg::after{content:'';position:absolute;inset:0;background:linear-gradient(120deg,rgba(5,50,44,.92) 0%,rgba(10,90,78,.82) 50%,rgba(20,120,100,.65) 100%);}
.hero-content{position:relative;z-index:2;max-width:700px;width:100%;}
.hero h1{font-size:46px;font-weight:800;color:#fff;line-height:1.16;margin-bottom:10px;}
.hero-sub{color:rgba(255,255,255,.78);font-size:15px;margin-bottom:34px;line-height:1.6;}

/* ── SEARCH BOX ── */
.search-box{background:#fff;border-radius:16px;padding:26px 28px 22px;box-shadow:0 12px 50px rgba(0,0,0,.22);max-width:720px;}
.search-row1{display:grid;grid-template-columns:1.6fr 1fr 1fr;gap:16px;margin-bottom:16px;}
.search-row2{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px;}
.s-field{display:flex;flex-direction:column;gap:5px;}
.s-field label{font-size:10.5px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.6px;}
.s-input-wrap{display:flex;align-items:center;gap:9px;border:1.5px solid #e4e4e4;border-radius:9px;padding:9px 13px;transition:.2s;background:#fff;}
.s-input-wrap:focus-within{border-color:#0d7c6b;box-shadow:0 0 0 3px rgba(13,124,107,.08);}
.s-input-wrap i{color:#0d7c6b;font-size:15px;flex-shrink:0;}
.s-input-wrap input,
.s-input-wrap select{border:none;outline:none;font-size:13px;font-family:'Poppins',sans-serif;width:100%;background:transparent;color:#333;}
.s-input-wrap select{cursor:pointer;appearance:none;-webkit-appearance:none;}
.select-wrap{position:relative;}
.select-wrap::after{content:'\f107';font-family:'Font Awesome 6 Free';font-weight:900;position:absolute;right:13px;top:50%;transform:translateY(-50%);color:#999;pointer-events:none;font-size:13px;}
.btn-search{width:100%;background:#0d7c6b;color:#fff;border:none;border-radius:10px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;letter-spacing:.3px;transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-search:hover{background:#0b6159;}

/* ── STATS ── */
.stats-bar{display:flex;background:#fff;border-bottom:1px solid #eee;}
.stat-item{flex:1;display:flex;flex-direction:column;align-items:center;padding:28px 10px;border-right:1px solid #eee;}
.stat-item:last-child{border-right:none;}
.stat-icon-wrap{width:50px;height:50px;border-radius:50%;background:#e7f5f2;display:flex;align-items:center;justify-content:center;margin-bottom:10px;font-size:20px;color:#0d7c6b;}
.stat-num{font-size:24px;font-weight:800;color:#111;line-height:1;}
.stat-label{font-size:12px;color:#888;margin-top:3px;text-align:center;}

/* ── DESTINATIONS ── */
.dest-outer{padding:50px 60px;background:#fff;}
.dest-dashed{border:2px dashed #b2dfe0;border-radius:20px;padding:30px 24px;}
.section-title{text-align:center;font-size:30px;font-weight:800;color:#111;margin-bottom:6px;}
.section-sub{text-align:center;font-size:13.5px;color:#999;margin-bottom:32px;}
.dest-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
.dest-card{position:relative;border-radius:13px;overflow:hidden;cursor:pointer;aspect-ratio:4/3;}
.dest-card img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s;}
.dest-card:hover img{transform:scale(1.07);}
.dest-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.72) 0%,rgba(0,0,0,.1) 55%,transparent 100%);}
.dest-info{position:absolute;bottom:14px;left:14px;color:#fff;}
.dest-tag{font-size:10px;background:rgba(255,255,255,.2);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.3);padding:3px 10px;border-radius:20px;margin-bottom:5px;display:inline-flex;align-items:center;gap:4px;}
.dest-name{font-size:19px;font-weight:700;text-shadow:0 1px 4px rgba(0,0,0,.4);}

/* ── WHY CHOOSE ── */
.why-section{background:#f5f8fb;padding:64px 60px;}
.why-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:20px;margin-top:36px;}
.why-card{background:#fff;border-radius:14px;padding:28px 16px 22px;text-align:center;box-shadow:0 3px 16px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;}
.why-card:hover{transform:translateY(-4px);box-shadow:0 8px 28px rgba(0,0,0,.10);}
.why-icon{width:54px;height:54px;border-radius:50%;background:#e6f4f1;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:22px;color:#0d7c6b;}
.why-card h4{font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:7px;}
.why-card p{font-size:11.5px;color:#888;line-height:1.65;}

/* ── TESTIMONIALS ── */
.testi-section{padding:64px 60px;background:#fff;}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:36px;}
.testi-card{background:#f8f9fb;border-radius:14px;padding:24px 22px;border:1px solid #eef0f3;}
.testi-head{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.testi-avatar{width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid #d4ede9;}
.testi-name{font-size:14px;font-weight:700;color:#1a1a1a;}
.testi-role{font-size:11px;color:#999;}
.testi-quote-icon{margin-left:auto;color:#0d7c6b;font-size:22px;opacity:.5;}
.stars{color:#f5a623;font-size:13px;margin-bottom:10px;letter-spacing:1px;}
.testi-text{font-size:13px;color:#555;line-height:1.75;}

/* ── FOOTER ── */
footer{background:#0d1a2e;color:#9aacbe;padding:56px 60px 24px;}
.footer-top{display:grid;grid-template-columns:2.2fr 1fr 1fr 1fr 1fr;gap:36px;margin-bottom:48px;}
.footer-brand{font-size:20px;font-weight:800;color:#fff;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.footer-brand .fb-icon{width:30px;height:30px;background:linear-gradient(135deg,#0d7c6b,#1aad96);border-radius:6px;display:flex;align-items:center;justify-content:center;}
.footer-brand .fb-icon i{color:#fff;font-size:14px;}
.footer-brand span{color:#0d7c6b;}
.footer-desc{font-size:12.5px;line-height:1.85;margin-bottom:18px;color:#7a8fa3;}
.f-contact{display:flex;align-items:flex-start;gap:9px;font-size:12.5px;margin-bottom:9px;color:#8aa0b5;}
.f-contact i{color:#0d7c6b;margin-top:2px;flex-shrink:0;}
.footer-col h5{color:#fff;font-size:13px;font-weight:700;margin-bottom:16px;letter-spacing:.3px;}
.footer-col ul{list-style:none;}
.footer-col ul li{margin-bottom:10px;}
.footer-col ul li a{color:#7a8fa3;text-decoration:none;font-size:12.5px;transition:.2s;}
.footer-col ul li a:hover{color:#0d7c6b;padding-left:4px;}
.newsletter-strip{background:#162234;border-radius:14px;padding:30px 36px;text-align:center;margin-bottom:36px;}
.newsletter-strip h4{color:#fff;font-size:16px;font-weight:700;margin-bottom:6px;}
.newsletter-strip p{font-size:12.5px;color:#7a8fa3;margin-bottom:18px;}
.nl-form{display:flex;gap:0;max-width:420px;margin:0 auto;}
.nl-form input{flex:1;padding:11px 16px;border:none;border-radius:8px 0 0 8px;font-size:13px;outline:none;font-family:'Poppins',sans-serif;background:#fff;}
.nl-form button{background:#0d7c6b;color:#fff;border:none;padding:11px 22px;border-radius:0 8px 8px 0;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:.2s;}
.nl-form button:hover{background:#0b6159;}
.footer-bottom{display:flex;align-items:center;justify-content:space-between;border-top:1px solid #1e2f45;padding-top:22px;}
.social-links{display:flex;gap:10px;}
.social-links a{width:34px;height:34px;border-radius:50%;background:#1a2d42;display:flex;align-items:center;justify-content:center;color:#7a8fa3;font-size:13px;text-decoration:none;transition:.2s;}
.social-links a:hover{background:#0d7c6b;color:#fff;}
.copy{font-size:12px;color:#4a6070;}

/* ══════════════════════════════
   RESPONSIVE
══════════════════════════════ */
@media(max-width:1024px){
  nav{padding:14px 30px;}
  .hero{padding:60px 30px 50px;}
  .dest-outer{padding:40px 30px;}
  .why-section{padding:50px 30px;}
  .testi-section{padding:50px 30px;}
  footer{padding:50px 30px 24px;}
  .why-grid{grid-template-columns:repeat(3,1fr);}
  .footer-top{grid-template-columns:1.8fr 1fr 1fr;}
  .footer-top .footer-col:nth-child(4),
  .footer-top .footer-col:nth-child(5){display:none;}
}
@media(max-width:768px){
  nav{padding:12px 20px;}
  .nav-links{display:none;}
  .nav-phone{display:none;}
  .nav-hamburger{display:block;}
  .nav-mobile-menu.open{display:flex;}
  .hero{padding:50px 20px 40px;min-height:auto;}
  .hero h1{font-size:30px;}
  .hero-sub{font-size:14px;}
  .search-row1,.search-row2{grid-template-columns:1fr;}
  .search-box{padding:20px 18px;}
  .stats-bar{flex-wrap:wrap;}
  .stat-item{flex:1 1 45%;border-right:none;border-bottom:1px solid #eee;padding:20px 10px;}
  .dest-outer{padding:30px 16px;}
  .dest-dashed{padding:20px 14px;}
  .dest-grid{grid-template-columns:1fr 1fr;}
  .section-title{font-size:24px;}
  .why-section{padding:40px 16px;}
  .why-grid{grid-template-columns:repeat(2,1fr);}
  .testi-section{padding:40px 16px;}
  .testi-grid{grid-template-columns:1fr;}
  footer{padding:40px 20px 20px;}
  .footer-top{grid-template-columns:1fr;gap:24px;}
  .footer-top .footer-col:nth-child(4),
  .footer-top .footer-col:nth-child(5){display:block;}
  .footer-bottom{flex-direction:column;gap:16px;text-align:center;}
  .newsletter-strip{padding:24px 20px;}
}
@media(max-width:480px){
  .hero h1{font-size:26px;}
  .dest-grid{grid-template-columns:1fr;}
  .why-grid{grid-template-columns:1fr;}
  .search-row1,.search-row2{grid-template-columns:1fr;}
  .nav-right .btn-login{display:none;}
}
</style>
</head>
<body>

<!-- ═══ NAV ═══ -->
<nav>
  <a class="nav-logo" href="#">
    <div class="nav-logo-icon"><i class="fa-solid fa-globe"></i></div>
    <span class="nav-logo-text">e<span>destinations</span></span>
  </a>
  <ul class="nav-links">
    <li><a href="#">Flights</a></li>
    <li><a href="#" class="active">Hotel</a></li>
    <li><a href="#">Rental</a></li>
    <li><a href="#">Visa</a></li>
    <li><a href="#">Cruises</a></li>
    <li><a href="#">My Bookings</a></li>
  </ul>
  <div class="nav-right">
    <div class="nav-phone"><i class="fa-solid fa-phone"></i> +1 (425) 576-4567</div>
    <button class="btn-login">Login</button>
    <button class="btn-signup">Sign Up</button>
    <button class="nav-hamburger" onclick="toggleMenu()" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
  </div>
</nav>
<div class="nav-mobile-menu" id="mobileMenu">
  <a href="#">Flights</a>
  <a href="#" class="active">Hotel</a>
  <a href="#">Rental</a>
  <a href="#">Visa</a>
  <a href="#">Cruises</a>
  <a href="#">My Bookings</a>
  <a href="#">Login</a>
  <a href="#" style="color:#0d7c6b;font-weight:700;">Sign Up</a>
</div>

<!-- ═══ HERO ═══ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <h1>Discover Luxury Stays<br/>Around the World</h1>
    <p class="hero-sub">Experience premium hospitality with exclusive deals on<br/>hotels, flights, and travel packages</p>

    <!-- SEARCH BOX -->
    <div class="search-box">
      <div class="search-row1">
        <!-- Destination Dropdown -->
        <div class="s-field">
          <label><i class="fa-solid fa-location-dot" style="color:#0d7c6b;margin-right:4px;"></i>Destination</label>
          <div class="s-input-wrap select-wrap">
            <i class="fa-solid fa-map-marker-alt"></i>
            <select>
              <option value="">Select destination</option>
              <option>Dubai, UAE</option>
              <option>Istanbul, Turkey</option>
              <option>Bangkok, Thailand</option>
              <option>Kuala Lumpur, Malaysia</option>
              <option>Karachi, Pakistan</option>
              <option>Makkah, Saudi Arabia</option>
              <option>London, UK</option>
              <option>Paris, France</option>
              <option>New York, USA</option>
            </select>
          </div>
        </div>
        <!-- Check In -->
        <div class="s-field">
          <label><i class="fa-regular fa-calendar" style="color:#0d7c6b;margin-right:4px;"></i>Check In</label>
          <div class="s-input-wrap">
            <i class="fa-regular fa-calendar"></i>
            <input type="date" />
          </div>
        </div>
        <!-- Check Out -->
        <div class="s-field">
          <label><i class="fa-regular fa-calendar" style="color:#0d7c6b;margin-right:4px;"></i>Check Out</label>
          <div class="s-input-wrap">
            <i class="fa-regular fa-calendar"></i>
            <input type="date" />
          </div>
        </div>
      </div>
      <div class="search-row2">
        <!-- Guests Dropdown -->
        <div class="s-field">
          <label><i class="fa-solid fa-user-group" style="color:#0d7c6b;margin-right:4px;"></i>Guests</label>
          <div class="s-input-wrap select-wrap">
            <i class="fa-solid fa-user"></i>
            <select>
              <option>1 Guest</option>
              <option selected>2 Guests</option>
              <option>3 Guests</option>
              <option>4 Guests</option>
              <option>5+ Guests</option>
            </select>
          </div>
        </div>
        <!-- Rooms Dropdown -->
        <div class="s-field">
          <label><i class="fa-solid fa-bed" style="color:#0d7c6b;margin-right:4px;"></i>Rooms</label>
          <div class="s-input-wrap select-wrap">
            <i class="fa-solid fa-door-open"></i>
            <select>
              <option selected>1 Room</option>
              <option>2 Rooms</option>
              <option>3 Rooms</option>
              <option>4+ Rooms</option>
            </select>
          </div>
        </div>
        <!-- Room Type -->
        <div class="s-field">
          <label><i class="fa-solid fa-star" style="color:#0d7c6b;margin-right:4px;"></i>Room Type</label>
          <div class="s-input-wrap select-wrap">
            <i class="fa-solid fa-hotel"></i>
            <select>
              <option>Any Type</option>
              <option>Standard</option>
              <option selected>Deluxe</option>
              <option>Suite</option>
              <option>Presidential</option>
            </select>
          </div>
        </div>
      </div>
      <button class="btn-search">
        <i class="fa-solid fa-magnifying-glass"></i> Search Hotels
      </button>
    </div>
  </div>
</section>

<!-- ═══ STATS ═══ -->
<div class="stats-bar">
  <div class="stat-item">
    <div class="stat-icon-wrap"><i class="fa-solid fa-users"></i></div>
    <div class="stat-num">50K+</div>
    <div class="stat-label">Happy Travelers</div>
  </div>
  <div class="stat-item">
    <div class="stat-icon-wrap"><i class="fa-solid fa-hotel"></i></div>
    <div class="stat-num">5,000+</div>
    <div class="stat-label">Partner Hotels</div>
  </div>
  <div class="stat-item">
    <div class="stat-icon-wrap"><i class="fa-solid fa-award"></i></div>
    <div class="stat-num">100%</div>
    <div class="stat-label">Best Price Guarantee</div>
  </div>
  <div class="stat-item">
    <div class="stat-icon-wrap"><i class="fa-solid fa-headset"></i></div>
    <div class="stat-num">24/7</div>
    <div class="stat-label">Customer Support</div>
  </div>
</div>

<!-- ═══ DESTINATIONS ═══ -->
<div class="dest-outer">
  <div class="dest-dashed">
    <h2 class="section-title">Explore Popular Destinations</h2>
    <p class="section-sub">Discover the world's most sought-after city and stay destinations</p>
    <div class="dest-grid">
      <div class="dest-card">
        <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80" alt="Dubai" loading="lazy"/>
        <div class="dest-overlay"></div>
        <div class="dest-info">
          <div class="dest-tag"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> UAE</div>
          <div class="dest-name">Dubai</div>
        </div>
      </div>
      <div class="dest-card">
        <img src="https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=600&q=80" alt="Istanbul" loading="lazy"/>
        <div class="dest-overlay"></div>
        <div class="dest-info">
          <div class="dest-tag"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> Turkey</div>
          <div class="dest-name">Istanbul</div>
        </div>
      </div>
      <div class="dest-card">
        <img src="https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=600&q=80" alt="Bangkok" loading="lazy"/>
        <div class="dest-overlay"></div>
        <div class="dest-info">
          <div class="dest-tag"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> Thailand</div>
          <div class="dest-name">Bangkok</div>
        </div>
      </div>
      <div class="dest-card">
        <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=600&q=80" alt="Kuala Lumpur" loading="lazy"/>
        <div class="dest-overlay"></div>
        <div class="dest-info">
          <div class="dest-tag"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> Malaysia</div>
          <div class="dest-name">Kuala Lumpur</div>
        </div>
      </div>
      <div class="dest-card">
        <img src="https://images.unsplash.com/photo-1567861911437-538298e4232c?w=600&q=80" alt="Karachi" loading="lazy"/>
        <div class="dest-overlay"></div>
        <div class="dest-info">
          <div class="dest-tag"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> Pakistan</div>
          <div class="dest-name">Karachi</div>
        </div>
      </div>
      <div class="dest-card">
        <img src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=600&q=80" alt="Makkah" loading="lazy"/>
        <div class="dest-overlay"></div>
        <div class="dest-info">
          <div class="dest-tag"><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> Saudi Arabia</div>
          <div class="dest-name">Makkah</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══ WHY CHOOSE US ═══ -->
<section class="why-section">
  <h2 class="section-title">Why Choose Us</h2>
  <p class="section-sub">Experience the difference with our premium travel services</p>
  <div class="why-grid">
    <div class="why-card">
      <div class="why-icon"><i class="fa-solid fa-bolt"></i></div>
      <h4>Easy Booking</h4>
      <p>Simple and fast booking process. Search, compare, book, and get instant confirmation.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fa-solid fa-shield-halved"></i></div>
      <h4>Secure Payments</h4>
      <p>All transactions are protected with end-to-end encryption for complete peace of mind.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fa-solid fa-globe"></i></div>
      <h4>Global Hotels</h4>
      <p>Access to over 5,000 premium partner hotels across more than 200 destinations worldwide.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fa-solid fa-circle-check"></i></div>
      <h4>Instant Confirmation</h4>
      <p>Receive booking confirmation instantly in real-time, available in multiple languages.</p>
    </div>
    <div class="why-card">
      <div class="why-icon"><i class="fa-solid fa-headset"></i></div>
      <h4>24/7 Support</h4>
      <p>Our dedicated support team is available round the clock to assist you anytime.</p>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIALS ═══ -->
<section class="testi-section">
  <h2 class="section-title">What Our Travelers Say</h2>
  <p class="section-sub">Real experiences from our satisfied customers</p>
  <div class="testi-grid">
    <div class="testi-card">
      <div class="testi-head">
        <img class="testi-avatar" src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah Johnson"/>
        <div>
          <div class="testi-name">Sarah Johnson</div>
          <div class="testi-role">Frequent Traveler</div>
        </div>
        <i class="fa-solid fa-quote-right testi-quote-icon"></i>
      </div>
      <div class="stars">★★★★★</div>
      <p class="testi-text">"Best travel booking experience I've ever had! The hotels were exactly as described and the customer service was absolutely exceptional. Highly recommended!"</p>
    </div>
    <div class="testi-card">
      <div class="testi-head">
        <img class="testi-avatar" src="https://randomuser.me/api/portraits/men/45.jpg" alt="Ahmed Al-Rashid"/>
        <div>
          <div class="testi-name">Ahmed Al-Rashid</div>
          <div class="testi-role">Business Traveler</div>
        </div>
        <i class="fa-solid fa-quote-right testi-quote-icon"></i>
      </div>
      <div class="stars">★★★★★</div>
      <p class="testi-text">"Outstanding service and incredibly easy booking. The customer support responds very quickly. Definitely my go-to platform for all luxury travel needs!"</p>
    </div>
    <div class="testi-card">
      <div class="testi-head">
        <img class="testi-avatar" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Emma Thompson"/>
        <div>
          <div class="testi-name">Emma Thompson</div>
          <div class="testi-role">Adventure Explorer</div>
        </div>
        <i class="fa-solid fa-quote-right testi-quote-icon"></i>
      </div>
      <div class="stars">★★★★★</div>
      <p class="testi-text">"Absolutely seamless from booking to check-out! Unbeatable prices and the team helped us at every step. This platform has transformed how I travel."</p>
    </div>
  </div>
</section>

<!-- ═══ FOOTER ═══ -->
<footer>
  <div class="footer-top">
    <div>
      <div class="footer-brand">
        <div class="fb-icon"><i class="fa-solid fa-globe"></i></div>
        e<span>destinations</span>
      </div>
      <p class="footer-desc">Your trusted partner for luxury travel experiences worldwide. Discover, book, and explore the world in premium style with exclusive deals.</p>
      <div class="f-contact"><i class="fa-solid fa-location-dot"></i> 527 Tower Street, New York, NY 10201</div>
      <div class="f-contact"><i class="fa-solid fa-phone"></i> +1 (425) 576-4567</div>
      <div class="f-contact"><i class="fa-solid fa-envelope"></i> info@edestinations.com</div>
    </div>
    <div class="footer-col">
      <h5>Company</h5>
      <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Careers</a></li>
        <li><a href="#">News</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Partnerships</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h5>Support</h5>
      <ul>
        <li><a href="#">Help Center</a></li>
        <li><a href="#">Contact Us</a></li>
        <li><a href="#">Terms of Service</a></li>
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Refund Policy</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h5>Quick Links</h5>
      <ul>
        <li><a href="#">Hotels</a></li>
        <li><a href="#">Flights</a></li>
        <li><a href="#">Packages</a></li>
        <li><a href="#">Visa Services</a></li>
        <li><a href="#">Cruises</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h5>Destinations</h5>
      <ul>
        <li><a href="#">Dubai</a></li>
        <li><a href="#">Bangkok</a></li>
        <li><a href="#">Istanbul</a></li>
        <li><a href="#">Makkah</a></li>
        <li><a href="#">Switzerland</a></li>
      </ul>
    </div>
  </div>

  <div class="newsletter-strip">
    <h4>Subscribe to Our Newsletter</h4>
    <p>Get exclusive deals and travel inspiration delivered straight to your inbox</p>
    <div class="nl-form">
      <input type="email" placeholder="Enter your email now..."/>
      <button>Subscribe</button>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="social-links">
      <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
      <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
      <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
    </div>
    <p class="copy">© 2024 Edestinations. All rights reserved.</p>
  </div>
</footer>

<script>
function toggleMenu(){
  var m=document.getElementById('mobileMenu');
  m.classList.toggle('open');
}
// Close mobile menu on resize
window.addEventListener('resize',function(){
  if(window.innerWidth>768){
    document.getElementById('mobileMenu').classList.remove('open');
  }
});
</script>
</body>
</html>