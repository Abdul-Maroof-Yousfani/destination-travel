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
html{scroll-behavior:smooth;}
body{font-family:'Poppins',sans-serif;color:#222;overflow-x:hidden;}

/* ── NAV ── */
nav{display:flex;align-items:center;justify-content:space-between;padding:0 56px;height:62px;background:#fff;position:sticky;top:0;z-index:300;box-shadow:0 2px 14px rgba(0,0,0,.09);}
.logo{display:flex;align-items:center;gap:9px;text-decoration:none;}
.logo-icon{width:34px;height:34px;background:linear-gradient(135deg,#0d7c6b,#25b99e);border-radius:8px;display:flex;align-items:center;justify-content:center;}
.logo-icon svg{width:18px;height:18px;fill:#fff;}
.logo-text{font-size:17px;font-weight:800;color:#111;letter-spacing:-.3px;}
.logo-text em{color:#0d7c6b;font-style:normal;}
.nav-links{display:flex;align-items:center;gap:2px;list-style:none;}
.nav-links a{text-decoration:none;color:#555;font-size:13px;font-weight:500;padding:6px 13px;border-radius:20px;white-space:nowrap;}
.nav-links a.active{background:#0d7c6b;color:#fff;}
.nav-links a:hover:not(.active){color:#0d7c6b;}
.nav-right{display:flex;align-items:center;gap:10px;}
.nav-phone{font-size:12.5px;font-weight:600;color:#222;display:flex;align-items:center;gap:5px;white-space:nowrap;}
.nav-phone i{color:#0d7c6b;font-size:12px;}
.btn-login{border:1.5px solid #0d7c6b;color:#0d7c6b;background:transparent;padding:6px 18px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;}
.btn-signup{background:#0d7c6b;color:#fff;border:none;padding:7px 18px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;}
.hamburger{display:none;background:none;border:none;font-size:21px;color:#333;cursor:pointer;}
.mob-menu{display:none;flex-direction:column;background:#fff;padding:14px 20px;gap:2px;border-top:1px solid #eee;position:sticky;top:62px;z-index:299;}
.mob-menu a{text-decoration:none;color:#444;font-size:13.5px;padding:9px 4px;border-bottom:1px solid #f5f5f5;font-weight:500;}
.mob-menu a.active{color:#0d7c6b;font-weight:700;}

/* ── HERO ── */
.hero{position:relative;padding:72px 56px 56px;min-height:500px;display:flex;align-items:center;overflow:hidden;}
.hero-img{position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1600&q=85') center/cover no-repeat;}
.hero-img::after{content:'';position:absolute;inset:0;background:linear-gradient(110deg,rgba(4,46,38,.95) 0%,rgba(7,80,66,.88) 40%,rgba(12,110,92,.72) 70%,rgba(20,140,116,.50) 100%);}
.hero-inner{position:relative;z-index:2;width:100%;display:flex;align-items:center;gap:32px;}
.hero-left{flex:1;min-width:0;}
.hero h1{font-size:44px;font-weight:800;color:#fff;line-height:1.15;margin-bottom:10px;}
.hero p{color:rgba(255,255,255,.76);font-size:14.5px;line-height:1.65;margin-bottom:32px;}

/* ── LATEST SEARCHES PANEL ── */
.ls-panel{position:relative;z-index:2;flex-shrink:0;background:rgba(15,30,45,.82);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border-radius:14px;border:1px solid rgba(255,255,255,.10);padding:20px 18px;align-self:flex-start;}
.ls-title{color:#fff;font-size:14px;font-weight:700;margin-bottom:14px;letter-spacing:.1px;}
.ls-list{display:flex;flex-direction:column;gap:8px;}
.ls-item{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.10);border-radius:8px;padding:9px 12px;cursor:pointer;transition:background .2s;}
.ls-item:hover{background:rgba(255,255,255,.13);}
.ls-item p{font-size:11px;color:rgba(255,255,255,.82);line-height:1.55;font-weight:400;}

/* ── SEARCH BOX ── */
.sbox{background:#fff;border-radius:14px;padding:22px 24px 20px;box-shadow:0 14px 55px rgba(0,0,0,.25);}
.sbox-row1{display:grid;grid-template-columns:1.7fr 1fr 1fr;gap:14px;margin-bottom:14px;}
.sbox-row2{display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:18px;}
.sf{display:flex;flex-direction:column;gap:4px;}
.sf label{font-size:10px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.7px;}
.si{display:flex;align-items:center;gap:8px;border:1.5px solid #e8e8e8;border-radius:8px;padding:8px 12px;transition:border-color .2s;}
.si:focus-within{border-color:#0d7c6b;}
.si i{color:#0d7c6b;font-size:14px;flex-shrink:0;}
.si input,.si select{border:none;outline:none;font-size:13px;font-family:'Poppins',sans-serif;width:100%;background:transparent;color:#333;appearance:none;-webkit-appearance:none;}
.si-sel{position:relative;}
.si-sel::after{content:'\f107';font-family:'Font Awesome 6 Free';font-weight:900;position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#bbb;font-size:12px;pointer-events:none;}
.guests-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.btn-search{width:100%;background:#0d7c6b;color:#fff;border:none;border-radius:9px;padding:13px;font-size:14.5px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;}
.btn-search:hover{background:#0b6055;}

/* ── STATS ── */
.stats{display:flex;background:#f8f9fa;border-bottom:1px solid #ececec;}
.stat{flex:1;display:flex;flex-direction:column;align-items:center;padding:30px 16px;border-right:1px solid #ececec;}
.stat:last-child{border-right:none;}
.stat-ic{width:52px;height:52px;border-radius:50%;background:#e0f5f0;display:flex;align-items:center;justify-content:center;margin-bottom:10px;color:#0d7c6b;font-size:21px;}
.stat-n{font-size:23px;font-weight:800;color:#111;line-height:1;}
.stat-l{font-size:11.5px;color:#888;margin-top:4px;text-align:center;font-weight:500;}

/* ── DESTINATIONS ── */
.dest-wrap{padding:56px 56px 50px;background:#fff;}
.sec-title{text-align:center;font-size:28px;font-weight:800;color:#111;margin-bottom:7px;}
.sec-sub{text-align:center;font-size:13px;color:#aaa;margin-bottom:34px;}
.dest-border{border:2px dashed #c0e8e4;border-radius:18px;padding:28px 22px;}
.dest-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:15px;}
.dc{position:relative;border-radius:12px;overflow:hidden;cursor:pointer;height:195px;background:#ddd;}
.dc img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s ease;}
.dc:hover img{transform:scale(1.08);}
.dc-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.75) 0%,transparent 55%);}
.dc-info{position:absolute;bottom:13px;left:13px;color:#fff;}
.dc-tag{font-size:9.5px;background:rgba(255,255,255,.18);backdrop-filter:blur(5px);border:1px solid rgba(255,255,255,.28);padding:2px 9px;border-radius:20px;margin-bottom:4px;display:inline-flex;align-items:center;gap:3px;letter-spacing:.2px;}
.dc-name{font-size:18px;font-weight:700;letter-spacing:-.2px;text-shadow:0 2px 6px rgba(0,0,0,.5);}

/* ── WHY CHOOSE ── */
.why{padding:60px 56px;background:linear-gradient(135deg,#e8f7f3 0%,#d0eeea 40%,#c5e9e3 100%);}
.why-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:18px;margin-top:36px;}
.wc{background:#fff;border-radius:13px;padding:26px 14px 20px;text-align:center;box-shadow:0 4px 18px rgba(13,124,107,.10);transition:transform .2s,box-shadow .2s;}
.wc:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(13,124,107,.16);}
.wc-ic{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#0d7c6b,#25b99e);display:flex;align-items:center;justify-content:center;margin:0 auto 13px;font-size:20px;color:#fff;}
.wc h4{font-size:12.5px;font-weight:700;color:#1a1a1a;margin-bottom:6px;}
.wc p{font-size:11px;color:#888;line-height:1.65;}

/* ── TESTIMONIALS ── */
.testi{padding:60px 56px;background:#fff;}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:36px;}
.tc{background:#fff;border-radius:13px;padding:24px 20px;border:1px solid #eaecef;box-shadow:0 3px 14px rgba(0,0,0,.05);}
.tc-head{display:flex;align-items:center;gap:11px;margin-bottom:12px;}
.tc-av{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #cce9e4;flex-shrink:0;}
.tc-name{font-size:13.5px;font-weight:700;color:#111;}
.tc-role{font-size:11px;color:#aaa;}
.tc-qi{margin-left:auto;color:#0d7c6b;font-size:20px;opacity:.4;}
.tc-stars{color:#f5a623;font-size:13px;letter-spacing:1px;margin-bottom:10px;}
.tc-text{font-size:12.5px;color:#555;line-height:1.78;}

/* ── FOOTER ── */
footer{background:#0c1824;color:#8da0b3;padding:52px 56px 22px;}
.ft-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:34px;margin-bottom:44px;}
.ft-brand{font-size:19px;font-weight:800;color:#fff;margin-bottom:13px;display:flex;align-items:center;gap:8px;}
.ft-brand-ic{width:28px;height:28px;background:linear-gradient(135deg,#0d7c6b,#25b99e);border-radius:6px;display:flex;align-items:center;justify-content:center;}
.ft-brand-ic i{color:#fff;font-size:13px;}
.ft-brand em{color:#0d7c6b;font-style:normal;}
.ft-desc{font-size:12px;line-height:1.85;color:#6a8099;margin-bottom:16px;}
.ft-con{display:flex;align-items:flex-start;gap:8px;font-size:12px;margin-bottom:9px;color:#6a8099;}
.ft-con i{color:#0d7c6b;margin-top:2px;flex-shrink:0;width:14px;}
.fc h5{color:#fff;font-size:12.5px;font-weight:700;margin-bottom:14px;letter-spacing:.2px;}
.fc ul{list-style:none;}
.fc ul li{margin-bottom:9px;}
.fc ul li a{color:#6a8099;text-decoration:none;font-size:12px;transition:.2s;}
.fc ul li a:hover{color:#0d7c6b;}
.nl-box{background:#162230;border-radius:12px;padding:28px 32px;text-align:center;margin-bottom:32px;}
.nl-box h4{color:#fff;font-size:15px;font-weight:700;margin-bottom:5px;}
.nl-box p{font-size:12px;color:#6a8099;margin-bottom:16px;}
.nl-form{display:flex;max-width:400px;margin:0 auto;}
.nl-form input{flex:1;padding:10px 15px;border:none;border-radius:7px 0 0 7px;font-size:12.5px;outline:none;font-family:'Poppins',sans-serif;background:#fff;}
.nl-form button{background:#0d7c6b;color:#fff;border:none;padding:10px 20px;border-radius:0 7px 7px 0;font-size:12.5px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;}
.ft-bottom{display:flex;align-items:center;justify-content:space-between;border-top:1px solid #1c2e40;padding-top:20px;}
.socials{display:flex;gap:9px;}
.socials a{width:32px;height:32px;border-radius:50%;background:#18293b;display:flex;align-items:center;justify-content:center;color:#6a8099;font-size:12.5px;text-decoration:none;transition:.2s;}
.socials a:hover{background:#0d7c6b;color:#fff;}
.copy{font-size:11.5px;color:#3e5468;}

/* ══ RESPONSIVE ══ */
@media(max-width:1080px){
  nav,.hero,.dest-wrap,.why,.testi,footer{padding-left:32px;padding-right:32px;}
  .why-grid{grid-template-columns:repeat(3,1fr);}
  .ft-grid{grid-template-columns:1.6fr 1fr 1fr;}
  .ft-grid .fc:nth-child(4),.ft-grid .fc:nth-child(5){display:none;}
  .ls-panel{width:260px;}
}
@media(max-width:900px){
  .hero-inner{flex-direction:column;}
  .ls-panel{width:100%;max-width:520px;}
  .ls-list{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
}
@media(max-width:768px){
  nav{padding:0 18px;}
  .nav-links,.nav-phone{display:none;}
  .hamburger{display:block;}
  .mob-menu.open{display:flex;}
  .hero{padding:36px 18px 32px;min-height:auto;}
  .hero h1{font-size:26px;}
  .hero p{font-size:13.5px;}
  .sbox-row1{grid-template-columns:1fr;}
  .guests-row{grid-template-columns:1fr;}
  .sbox{padding:18px 16px 16px;}
  .ls-list{grid-template-columns:1fr;}
  .stats{flex-wrap:wrap;}
  .stat{flex:1 1 48%;border-right:none;border-bottom:1px solid #ececec;padding:20px 12px;}
  .dest-wrap,.why,.testi{padding:36px 18px;}
  .dest-grid{grid-template-columns:1fr 1fr;}
  .sec-title{font-size:22px;}
  .why-grid{grid-template-columns:repeat(2,1fr);}
  .testi-grid{grid-template-columns:1fr;}
  footer{padding:36px 18px 18px;}
  .ft-grid{grid-template-columns:1fr;gap:22px;}
  .ft-grid .fc:nth-child(4),.ft-grid .fc:nth-child(5){display:block;}
  .ft-bottom{flex-direction:column;gap:14px;}
  .nl-box{padding:20px 14px;}
}
@media(max-width:480px){
  .hero h1{font-size:22px;}
  .dest-grid{grid-template-columns:1fr;}
  .why-grid{grid-template-columns:1fr;}
  .nav-right .btn-login{display:none;}
  .sbox-row1,.sbox-row2{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a class="logo" href="#">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
    </div>
    <span class="logo-text">e<em>destinations</em></span>
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
    <div class="nav-phone"><i class="fa-solid fa-phone"></i>+1 (425) 576-4567</div>
    <button class="btn-login">Login</button>
    <button class="btn-signup">Sign Up</button>
    <button class="hamburger" onclick="document.getElementById('mm').classList.toggle('open')"><i class="fa-solid fa-bars"></i></button>
  </div>
</nav>
<div class="mob-menu" id="mm">
  <a href="#">Flights</a><a href="#" class="active">Hotel</a><a href="#">Rental</a>
  <a href="#">Visa</a><a href="#">Cruises</a><a href="#">My Bookings</a>
  <a href="#">Login</a><a href="#" style="color:#0d7c6b;font-weight:700;">Sign Up</a>
</div>

<!-- HERO -->
<section class="hero">
  <div class="hero-img"></div>
  <div class="hero-inner">

    <!-- LEFT: heading + search -->
    <div class="hero-left">
      <h1>Discover Luxury Stays<br/>Around the World</h1>
      <p>Experience premium hospitality with exclusive deals on hotels,<br/>flights, and travel packages</p>
      <div class="sbox">
        <div class="sbox-row1">
          <div class="sf">
            <label>Destination</label>
            <div class="si si-sel">
              <i class="fa-solid fa-location-dot"></i>
              <select>
                <option value="">Dubai</option>
                <option>Istanbul, Turkey</option>
                <option>Bangkok, Thailand</option>
                <option>Kuala Lumpur, Malaysia</option>
                <option>Karachi, Pakistan</option>
                <option>Makkah, Saudi Arabia</option>
                <option>London, UK</option>
                <option>Paris, France</option>
              </select>
            </div>
          </div>
          <div class="sf">
            <label>Check-in</label>
            <div class="si">
              <i class="fa-regular fa-calendar"></i>
              <input type="date" placeholder="mm/dd/yyyy"/>
            </div>
          </div>
          <div class="sf">
            <label>Check-out</label>
            <div class="si">
              <i class="fa-regular fa-calendar"></i>
              <input type="date" placeholder="mm/dd/yyyy"/>
            </div>
          </div>
        </div>
        <div class="guests-row" style="margin-bottom:18px;">
          <div class="sf">
            <label>Guests &amp; Rooms</label>
            <div class="si si-sel">
              <i class="fa-solid fa-user-group"></i>
              <select>
                <option>1 Guest, 1 Room</option>
                <option selected>2 Guests, 1 Room</option>
                <option>3 Guests, 1 Room</option>
                <option>4 Guests, 2 Rooms</option>
                <option>5+ Guests, 2+ Rooms</option>
              </select>
            </div>
          </div>
          <div class="sf">
            <label>Room Type</label>
            <div class="si si-sel">
              <i class="fa-solid fa-bed"></i>
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
        <button class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search Hotels</button>
      </div>
    </div>

    <!-- RIGHT: Latest searches panel -->
    <div class="ls-panel">
      <div class="ls-title"><i class="fa-solid fa-clock-rotate-left" style="margin-right:7px;color:#25b99e;font-size:13px;"></i>Latest 10 searches</div>
      <div class="ls-list" id="lsList"></div>
    </div>

  </div>
</section>

<!-- STATS -->
<div class="stats">
  <div class="stat"><div class="stat-ic"><i class="fa-solid fa-users"></i></div><div class="stat-n">50K+</div><div class="stat-l">Happy Travelers</div></div>
  <div class="stat"><div class="stat-ic"><i class="fa-solid fa-hotel"></i></div><div class="stat-n">5,000+</div><div class="stat-l">Partner Hotels</div></div>
  <div class="stat"><div class="stat-ic"><i class="fa-solid fa-award"></i></div><div class="stat-n">100%</div><div class="stat-l">Best Price Guarantee</div></div>
  <div class="stat"><div class="stat-ic"><i class="fa-solid fa-headset"></i></div><div class="stat-n">24/7</div><div class="stat-l">Customer Support</div></div>
</div>

<!-- DESTINATIONS -->
<div class="dest-wrap">
  <h2 class="sec-title">Explore Popular Destinations</h2>
  <p class="sec-sub">Discover the world's most sought-after luxury destinations</p>
  <div class="dest-border">
    <div class="dest-grid" id="dGrid"></div>
  </div>
</div>

<!-- WHY CHOOSE -->
<section class="why">
  <h2 class="sec-title">Why Choose Us</h2>
  <p class="sec-sub">Experience the difference with our premium travel services</p>
  <div class="why-grid">
    <div class="wc"><div class="wc-ic"><i class="fa-solid fa-bolt"></i></div><h4>Easy Booking</h4><p>Simple and fast booking with secure confirmation at every step of your journey.</p></div>
    <div class="wc"><div class="wc-ic"><i class="fa-solid fa-shield-halved"></i></div><h4>Secure Payments</h4><p>All payments secured with end-to-end encryption and multiple secure protections.</p></div>
    <div class="wc"><div class="wc-ic"><i class="fa-solid fa-globe"></i></div><h4>Global Hotels</h4><p>Access to 5,000+ premium partner hotels across 200+ destinations worldwide.</p></div>
    <div class="wc"><div class="wc-ic"><i class="fa-solid fa-circle-check"></i></div><h4>Instant Confirmation</h4><p>Real-time booking confirmation in multiple languages across the globe.</p></div>
    <div class="wc"><div class="wc-ic"><i class="fa-solid fa-headset"></i></div><h4>24/7 Support</h4><p>Dedicated round-the-clock support in multiple languages anytime you need.</p></div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testi">
  <h2 class="sec-title">What Our Travelers Say</h2>
  <p class="sec-sub">Real experiences from our satisfied customers</p>
  <div class="testi-grid" id="tGrid"></div>
</section>

<!-- FOOTER -->
<footer>
  <div class="ft-grid">
    <div>
      <div class="ft-brand">
        <div class="ft-brand-ic"><i class="fa-solid fa-globe"></i></div>
        e<em>destinations</em>
      </div>
      <p class="ft-desc">Your trusted partner for luxury travel experiences worldwide. Discover, book, and explore the world in premium style with exclusive deals.</p>
      <div class="ft-con"><i class="fa-solid fa-location-dot"></i>527 Tower Street, New York, NY 10201</div>
      <div class="ft-con"><i class="fa-solid fa-phone"></i>+1 (425) 576-4567</div>
      <div class="ft-con"><i class="fa-solid fa-envelope"></i>info@edestinations.com</div>
    </div>
    <div class="fc"><h5>Company</h5><ul><li><a href="#">About Us</a></li><li><a href="#">Careers</a></li><li><a href="#">News</a></li><li><a href="#">Blog</a></li><li><a href="#">Partnerships</a></li></ul></div>
    <div class="fc"><h5>Support</h5><ul><li><a href="#">Help Center</a></li><li><a href="#">Contact Us</a></li><li><a href="#">Terms of Service</a></li><li><a href="#">Privacy Policy</a></li><li><a href="#">Refund Policy</a></li></ul></div>
    <div class="fc"><h5>Quick Links</h5><ul><li><a href="#">Hotels</a></li><li><a href="#">Flights</a></li><li><a href="#">Packages</a></li><li><a href="#">Visa Services</a></li><li><a href="#">Cruises</a></li></ul></div>
    <div class="fc"><h5>Destinations</h5><ul><li><a href="#">Dubai</a></li><li><a href="#">Bangkok</a></li><li><a href="#">Istanbul</a></li><li><a href="#">Makkah</a></li><li><a href="#">Switzerland</a></li></ul></div>
  </div>
  <div class="nl-box">
    <h4>Subscribe to Our Newsletter</h4>
    <p>Get exclusive deals and travel inspiration delivered straight to your inbox</p>
    <div class="nl-form"><input type="email" placeholder="Enter your email now..."/><button>Subscribe</button></div>
  </div>
  <div class="ft-bottom">
    <div class="socials">
      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
      <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
    </div>
    <p class="copy">© 2024 Edestinations. All rights reserved.</p>
  </div>
</footer>

<script>
// Latest 10 searches panel
var searches=[
  'Exploring Tokyo, Japan from 20 May 2026 to 25 May 2026 (5 Nights) with 1 passenger',
  'Going to Dubai, United Arab Emirates from 17 Apr 2026 to 18 Apr 2026 (1 Night) with 2 passengers',
  'Business trip to New York, USA from 1 Jun 2026 to 4 Jun 2026 (3 Nights) with 3 passengers',
  'Family vacation in Paris, France from 10 Jul 2026 to 17 Jul 2026 (7 Nights) with 4 passengers',
  'Weekend getaway to Sydney, Australia from 5 Aug 2026 to 7 Aug 2026 (2 Nights) with 2 passengers',
  'Conference in Berlin, Germany from 15 Sep 2026 to 18 Sep 2026 (3 Nights) with 1 passenger',
];
var lsList=document.getElementById('lsList');
searches.forEach(function(s){
  var item=document.createElement('div');
  item.className='ls-item';
  item.innerHTML='<p>'+s+'</p>';
  item.onclick=function(){
    var dest=s.split(' from ')[0].replace('Exploring ','').replace('Going to ','').replace('Business trip to ','').replace('Family vacation in ','').replace('Weekend getaway to ','').replace('Conference in ','').replace('Honeymoon in ','').replace('City break in ','').replace('Holiday in ','').replace('New Year in ','');
    document.querySelector('.si select').value=dest.split(',')[0];
  };
  lsList.appendChild(item);
});

// Destination cards — using Unsplash Source (no API key needed, redirects to real photos)
var dests = [
  {name:'Dubai',     country:'UAE',          url:'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80'},
  {name:'Istanbul',  country:'Turkey',       url:'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=600&q=80'},
  {name:'Bangkok',   country:'Thailand',     url:'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=600&q=80'},
  {name:'Kuala Lumpur',country:'Malaysia',   url:'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=600&q=80'},
  {name:'Karachi',   country:'Pakistan',     url:'https://images.unsplash.com/photo-1567861911437-538298e4232c?w=600&q=80'},
  {name:'Makkah',    country:'Saudi Arabia', url:'https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=600&q=80'}
];

// Fallback picsum seeds if unsplash fails
var fallbacks=['https://picsum.photos/seed/city1/600/420','https://picsum.photos/seed/city2/600/420','https://picsum.photos/seed/city3/600/420','https://picsum.photos/seed/city4/600/420','https://picsum.photos/seed/city5/600/420','https://picsum.photos/seed/city6/600/420'];

var dg=document.getElementById('dGrid');
dests.forEach(function(d,i){
  var c=document.createElement('div');
  c.className='dc';
  c.innerHTML='<img src="'+d.url+'" alt="'+d.name+'" loading="lazy" onerror="this.src=\''+fallbacks[i]+'\'"/><div class="dc-overlay"></div><div class="dc-info"><div class="dc-tag"><i class="fa-solid fa-location-dot" style="font-size:8px;"></i> '+d.country+'</div><div class="dc-name">'+d.name+'</div></div>';
  dg.appendChild(c);
});

// Testimonials
var testis=[
  {name:'Sarah Johnson',role:'Frequent Traveler',color:'0d7c6b',
   text:'"The most amazing travel booking experience I\'ve ever had! The hotels were exactly as described and the customer service was exceptional. Will definitely use again!"'},
  {name:'Ahmed Al-Rashid',role:'Business Traveler',color:'1565c0',
   text:'"Absolutely outstanding service! The booking process is so easy and the customer support responds very quickly. I strongly recommend this for luxury travel!"'},
  {name:'Emma Thompson',role:'Adventure Explorer',color:'7b1fa2',
   text:'"Totally loved this platform! Everything was seamless from booking to check-out. The prices were unbeatable and the team helped at every step of our trip."'}
];

var tg=document.getElementById('tGrid');
testis.forEach(function(t){
  var c=document.createElement('div');
  c.className='tc';
  c.innerHTML='<div class="tc-head"><img class="tc-av" src="https://ui-avatars.com/api/?name='+encodeURIComponent(t.name)+'&background='+t.color+'&color=fff&size=88&bold=true" alt="'+t.name+'"/><div><div class="tc-name">'+t.name+'</div><div class="tc-role">'+t.role+'</div></div><i class="fa-solid fa-quote-right tc-qi"></i></div><div class="tc-stars">★★★★★</div><p class="tc-text">'+t.text+'</p>';
  tg.appendChild(c);
});
</script>
</body>
</html>