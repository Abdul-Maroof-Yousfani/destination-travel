<!DOCTYPE html>
<html lang="en">
   <head>
      <?php include("includes/compatibility.php"); ?>
      <meta name="description" content="">
      <title>Home | Dbookers.com</title>
      <?php include("includes/style.php"); ?>
   </head>
   <body>
      <?php include("includes/header.php"); ?>
      <section class="mainBanner wow fadeInLeft" style="background-image:url(assets/images/banner/banner.png); ">
         <div class="container">
            <div class="row">
               <div class="col-md-12 col-lg-12">
                  <div class="banner">
                     <div class="row align-items-center">
                        <div class="col-md-12 col-lg-4">
                           <div class="tab-links">
                              <ul class="tab-product  wow fadeInRight">
                                 <li data-targetit="box-1" class="current">
                                    <a href="#tab-1" data-toggle="tab"><i class="fa-solid fa-plane-departure"></i> Flights</a>
                                 </li>
                                 <li data-targetit="box-2" >
                                    <a href="#tab-2" data-toggle="tab"><i class="fa-solid fa-globe"></i> Tours</a>
                                 </li>
                                 <li data-targetit="box-3" >
                                    <a href="#tab-3" data-toggle="tab"><i class="fa-solid fa-hotel"></i> Hotels</a>
                                 </li>
                                 <li data-targetit="box-4" >
                                    <a href="#tab-4" data-toggle="tab"><i class="fa-solid fa-passport"></i> Visa </a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                        <div class="col-md-12 col-lg-8">
                           <div class="tab-head">
                              <h2>Explore beautiful places in the world </h2>
                           </div>
                        </div>
                     </div>
               
                     <div class="box-1 showfirst   tab-content">
                        <div class="radio-container">
                           <div>
                              <input type="radio" id="option1" name="options" value="option1">
                              <label for="option1">One Way</label>
                           </div>
                           <div>
                              <input type="radio" id="option2" name="options" value="option2">
                              <label for="option2">Return</label>
                           </div>
                           <div>
                              <input type="radio" id="option3" name="options" value="option3">
                              <label for="option3">Multi City</label>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle1">
                                 <span class="selected-country">
                                    <!-- Default Selected Country -->
                                    <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                                 <div class="dropdown-item" data-value="one">One</div>
                                 <div class="dropdown-item" data-value="two">Two</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="three">Three</div>
                                       <div class="dropdown-item" data-value="four">Four</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle2">
                                 <span class="selected-country">
                                    Economy
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                                 <div class="dropdown-item" data-value="five">Five</div>
                                 <div class="dropdown-item" data-value="six">Six</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="seven">Seven</div>
                                       <div class="dropdown-item" data-value="eight">Eight</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="fly">
                           <ul>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="right-left">
                                    <i class="fa-solid fa-right-left"></i>
                                 </div>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Check in</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Check Out</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="search-container">
                                    <a href="search.php"><i class="fa-solid fa-magnifying-glass"></i></a>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>



                     <div class="box-2  tab-content">
                        <div class="radio-container">
                           <div>
                              <input type="radio" id="option1" name="options" value="option1">
                              <label for="option1">One Way</label>
                           </div>
                           <div>
                              <input type="radio" id="option2" name="options" value="option2">
                              <label for="option2">Return</label>
                           </div>
                           <div>
                              <input type="radio" id="option3" name="options" value="option3">
                              <label for="option3">Multi City</label>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle1">
                                 <span class="selected-country">
                                    <!-- Default Selected Country -->
                                    <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                                 <div class="dropdown-item" data-value="one">One</div>
                                 <div class="dropdown-item" data-value="two">Two</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="three">Three</div>
                                       <div class="dropdown-item" data-value="four">Four</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle2">
                                 <span class="selected-country">
                                    Economy
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                                 <div class="dropdown-item" data-value="five">Five</div>
                                 <div class="dropdown-item" data-value="six">Six</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="seven">Seven</div>
                                       <div class="dropdown-item" data-value="eight">Eight</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="fly">
                           <ul>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="right-left">
                                    <i class="fa-solid fa-right-left"></i>
                                 </div>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Check in</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Check Out</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="search-container">
                                    <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>


                     <div class="box-3  tab-content">
                        <div class="radio-container">
                           <div>
                              <input type="radio" id="option1" name="options" value="option1">
                              <label for="option1">One Way</label>
                           </div>
                           <div>
                              <input type="radio" id="option2" name="options" value="option2">
                              <label for="option2">Return</label>
                           </div>
                           <div>
                              <input type="radio" id="option3" name="options" value="option3">
                              <label for="option3">Multi City</label>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle1">
                                 <span class="selected-country">
                                    <!-- Default Selected Country -->
                                    <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                                 <div class="dropdown-item" data-value="one">One</div>
                                 <div class="dropdown-item" data-value="two">Two</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="three">Three</div>
                                       <div class="dropdown-item" data-value="four">Four</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle2">
                                 <span class="selected-country">
                                    Economy
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                                 <div class="dropdown-item" data-value="five">Five</div>
                                 <div class="dropdown-item" data-value="six">Six</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="seven">Seven</div>
                                       <div class="dropdown-item" data-value="eight">Eight</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="fly">
                           <ul>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="right-left">
                                    <i class="fa-solid fa-right-left"></i>
                                 </div>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Check in</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Check Out</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="search-container">
                                    <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>


                     <div class="box-4 tab-content">
                        <div class="radio-container">
                           <div>
                              <input type="radio" id="option1" name="options" value="option1">
                              <label for="option1">One Way</label>
                           </div>
                           <div>
                              <input type="radio" id="option2" name="options" value="option2">
                              <label for="option2">Return</label>
                           </div>
                           <div>
                              <input type="radio" id="option3" name="options" value="option3">
                              <label for="option3">Multi City</label>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle1">
                                 <span class="selected-country">
                                    <!-- Default Selected Country -->
                                    <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                                 <div class="dropdown-item" data-value="one">One</div>
                                 <div class="dropdown-item" data-value="two">Two</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="three">Three</div>
                                       <div class="dropdown-item" data-value="four">Four</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div>
                              <div class="dropdown-toggle" id="dropdownToggle2">
                                 <span class="selected-country">
                                    Economy
                                 </span>
                                 </div>
                                 <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                                 <div class="dropdown-item" data-value="five">Five</div>
                                 <div class="dropdown-item" data-value="six">Six</div>

                                 <!-- Nested Dropdown -->
                                 <div class="dropdown-item">
                                    <span>More Options</span>
                                    <div class="nested-dropdown" style="display: none;">
                                       <div class="dropdown-item" data-value="seven">Seven</div>
                                       <div class="dropdown-item" data-value="eight">Eight</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="fly">
                           <ul>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="right-left">
                                    <i class="fa-solid fa-right-left"></i>
                                 </div>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-location-dot"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Flying From (City or Airport)</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys flys2">
                                          <p>Check in</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <a href="#">
                                    <div class="main-flex">
                                       <div class="icon-head-loc">
                                          <i class="fa-solid fa-calendar-days"></i>
                                       </div>
                                       <div class="flys">
                                          <p>Check Out</p>
                                       </div>
                                    </div>
                                 </a>
                              </li>
                              <li>
                                 <div class="search-container">
                                    <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>      
         </div>
      </section>

      <section class="help-line wow fadeInRight">
         <div class="container">
            <div class="row">
               <div class="col-md-12 col-lg-12">
                  <div class="hep">
                     <ul>
                        <li><a href="tel:92 01234567 0">
                              <div class="main-flex">
                                 <div class="icon-head-help"><i class="fa-solid fa-headphones"></i></div>
                                 <div class="call-content"><span>Call 24/7 24/7 Customer Support </span><br> <strong>Speak travel expert</strong></div>
                              </div>
                           </a>
                        </li>
                        <li><a href="#"><i class="fa-solid fa-phone"></i> Call: +92 012345678 9</a></li>
                        <li><a href="#"><i class="fa-brands fa-whatsapp"></i> Call: +92 012345678 9</a></li>
                     </ul>
                  </div>
               </div>
            </div>      
         </div>
      </section>

      <section class="testimonials wow fadeInLeft" style="background-image:url(assets/images/banner/testimonial.png); ">
         <div class="container">
            <div class="row">
               <div class="col-md-12 col-lg-12">
                  <div class="testi">
                     <h2>Testimonials</h2>
                     <ul class="index-slider">
                        <li>
                           <div class="tes-bo">
                              <div class="tes-img">
                                 <img src="assets/images/tes1.png" alt="">
                              </div>
                              <div class="tesfelx">
                                 <div class="tes-prof">
                                    <h4>Sebastian</h4>
                                    <h5>Graphic design</h5>
                                 </div>
                                 <div class="start">
                                    <ul>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                              <div class="tes-content">
                                 <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text.</p>
                              </div>
                           </div>
                        </li>
                        <li>
                           <div class="tes-bo">
                              <div class="tes-img">
                                 <img src="assets/images/tes2.png" alt="">
                              </div>
                              <div class="tesfelx">
                                 <div class="tes-prof">
                                    <h4>Evangeline</h4>
                                    <h5>Model</h5>
                                 </div>
                                 <div class="start">
                                    <ul>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                              <div class="tes-content">
                                 <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text.</p>
                              </div>
                           </div>
                        </li>
                        <li>
                           <div class="tes-bo">
                              <div class="tes-img">
                                 <img src="assets/images/tes3.png" alt="">
                              </div>
                              <div class="tesfelx">
                                 <div class="tes-prof">
                                    <h4>Alexander</h4>
                                    <h5>Software engineer</h5>
                                 </div>
                                 <div class="start">
                                    <ul>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                              <div class="tes-content">
                                 <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text.</p>
                              </div>
                           </div>
                        </li>
                        <li>
                           <div class="tes-bo">
                              <div class="tes-img">
                                 <img src="assets/images/tes2.png" alt="">
                              </div>
                              <div class="tesfelx">
                                 <div class="tes-prof">
                                    <h4>Evangeline</h4>
                                    <h5>Model</h5>
                                 </div>
                                 <div class="start">
                                    <ul>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                       <li>
                                          <i class="fa-solid fa-star"></i>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                              <div class="tes-content">
                                 <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text.</p>
                              </div>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>      
         </div>
      </section>

      <section class="supourt wow fadeInRight">
         <div class="container">
            <div class="row">
               <div class="col-md-12 col-lg-12">
                  <div class="supo">
                     <ul>
                        <li>
                           <div class="sup-bo">
                              <img src="assets/images/sup1.png" alt="">
                              <h2>24/7 Customer Support</h2>
                           </div>
                        </li>
                        <li>
                           <div class="sup-bo">
                              <img src="assets/images/sup2.png" alt="">
                              <h2>Refunds within 48 hours</h2>
                           </div>
                        </li>
                        <li>
                           <div class="sup-bo">
                              <img src="assets/images/sup3.png" alt="">
                              <h2>Secure Transaction Guaranteed</h2>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>      
         </div>
      </section>

      <section class="flight wow fadeInLeft">
         <div class="container">
            <div class="row">
               <div class="col-md-12 col-lg-12">
                  <div class="supo">
                     <ul>
                        <li>
                           <div class="sup-bo">
                              <i class="fa-solid fa-plane-arrival"></i>
                              <div id="shiva"><span class="count">700</span>k+</div>
                              <p>Flights booked</p>
                           </div>
                        </li>
                        <li>
                           <div class="sup-bo">
                              <i class="fa-solid fa-bus"></i>
                              <div id="shiva"><span class="count">300</span>k+</div>
                              <p>Buses booked</p>
                           </div>
                        </li>
                        <li>
                           <div class="sup-bo">
                              <i class="fa-solid fa-house"></i>
                              <div id="shiva"><span class="count">50</span>k+</div>
                              <p>Hotels booked</p>
                           </div>
                        </li>
                        <li>
                           <div class="sup-bo">
                              <i class="fa-solid fa-gauge"></i>
                              <div id="shiva"><span class="count">20</span>m+</div>
                              <p>Kilometres traveled</p>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>      
         </div>
      </section>

      <section class="featured wow fadeInRight">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-md-12 col-lg-3">
                  <div class="fea">
                     <h2>Featured Partners</h2>
                     <p>Domestic & International</p>
                  </div>
               </div>
               <div class="col-md-12 col-lg-9">
                  <div class="feaul">
                     <ul class="m-silder">
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client2.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client3.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client4.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client5.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client6.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client7.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client8.png" alt="">
                           </div>
                        </li>
                        <li>
                           <div class="fea-img">
                              <img src="assets/images/client5.png" alt="">
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>      
         </div>
      </section>
      
      <?php include("includes/footer.php"); ?>
      <?php include("includes/scripts.php"); ?>
   </body>
</html>