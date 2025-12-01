<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome & Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
 .flys{position:relative;}
.flys input{width:100%;}
.dropdown-list{position:absolute;top:100%;left:0;right:0;z-index:100;background:#fff;border:1px solid #ccc;max-height:200px;overflow-y:auto;display:none;}
.dropdown-list div{padding:10px;cursor:pointer;}
.dropdown-list div:hover{background-color:#f0f0f0;}
.modern-calendar{padding:12px 16px;border:1px solid #ccc;border-radius:12px;font-size:16px;outline:none;transition:border-color 0.3s,box-shadow 0.3s;background-color:#f9f9f9;color:#333;}
.modern-calendar:focus{border-color:#4A90E2;box-shadow:0 0 0 3px rgba(74,144,226,0.2);}
.modern-calendar{padding:12px 16px;border:2px solid #ccc;border-radius:12px;font-size:16px;outline:none;transition:border-color 0.3s ease,box-shadow 0.3s ease;background-color:#f9f9f9;color:#333;}
.modern-calendar:focus{border-color:#4A90E2;box-shadow:0 0 0 3px rgba(74,144,226,0.2);}
.modern-calendar::-webkit-calendar-picker-indicator{background-color:#4A90E2;border-radius:50%;padding:4px;cursor:pointer;transition:background-color 0.3s ease;}
.modern-calendar::-webkit-calendar-picker-indicator:hover{background-color:#357ABD;}
.booking-section{background:#fff;border-radius:15px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,.1);}
.nav-tabs .nav-link.active{background:#0d6efd;color:#fff!important;border:none;}
.nav-tabs .nav-link{color:#0d6efd;font-weight:500;}
.search-btn{background:#0d6efd;color:#fff;border-radius:10px;font-weight:500;}
.search-btn:hover{background:#0b5ed7;}
.input-group-text{background-color:#fff;cursor:pointer;transition:all 0.3s ease;color:#00839d;}
.input-group-text:hover{color:#000;}
.form-control{height:48px;padding:12px 16px;border:1px solid #e0e0e0;border-radius:8px;font-size:15px;color:#333;background-color:#fff !important;transition:all 0.2sease;}
</style>
@if (session('error'))
    <script>
        window.onload = function () {
            let error = @json(session('error'));
            if (typeof _alert === "function") {
                _alert(error, 'error');
            } else {
                console.error("Function _alert is not defined yet.");
            }
        };
    </script>
@endif
@if (session('message'))
    <script>
        window.onload = function () {
            let message = @json(session('message'));
            if (typeof _alert === "function") {
                _alert(message, 'message');
            } else {
                console.error("Function _alert is not defined yet.");
            }
        };
    </script>
@endif
<script>
    function toggleDropdown() {
        const menu = document.getElementById('dropdownMenu2');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
    function updateSelection(radio) {
        const dropdownToggle = document.getElementById('dropdownToggle2');
        const selectedText = dropdownToggle.querySelector(".selected-country"); // Ensure correct selection
        selectedText.textContent = radio.parentElement.textContent.trim();
        document.getElementById('dropdownMenu2').style.display = 'none'; // Close dropdown
    }
</script>
<div class="banner">
    <div class="row align-items-center">
        <div class="col-md-12 col-lg-5">
            <div class="tab-links">
                <ul class="tab-product  wow fadeInRight">
                    <li data-targetit="box-1" class="current">
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-plane-departure"></i> Flights</a>
                    </li>
                    <li data-targetit="box-2" >
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-globe"></i> Tours</a>
                    </li>
                    <li data-targetit="box-3" >
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-hotel"></i> Hotels</a>
                    </li>
                    <li data-targetit="box-4" >
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-passport"></i> Visa </a>
                    </li>
                </ul>
            </div>
            <div class="tab-links tab-links-mob">
                <ul class="tab-product  wow fadeInRight">
                    <li data-targetit="box-1" class="current">
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-plane-departure"></i></a>
                    </li>
                    <li data-targetit="box-2" >
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-globe"></i></a>
                    </li>
                    <li data-targetit="box-3" >
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-hotel"></i></a>
                    </li>
                    <li data-targetit="box-4" >
                    <a class="pointer" data-toggle="tab"><i class="fa-solid fa-passport"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-12 col-lg-7">
            <div class="tab-head">
                <h2>Explore beautiful places in the world </h2>
            </div>
        </div>
    </div>
    <div class="box-1 showfirst  tab-content">
        <div class="radio-container">
            <div>
                <input type="radio" id="oneWaySearch" name="searchOptions" value="oneWaySearch" checked>
                <label for="oneWaySearch">One Way</label>
            </div>
            <div>
                <input type="radio" id="returnSearch" name="searchOptions" value="returnSearch">
                <label for="returnSearch">Return</label>
            </div>
            {{-- <div>
                <input type="radio" id="connectedSearch" name="searchOptions" value="connectedSearch">
                <label for="connectedSearch">Multi City</label>
            </div> --}}

            <div class="dropdowns" style="display: flex; gap: 15px;">
                <!-- Adults -->
                <div class="dropdown">
                    <div class="dropdown-toggle" id="dropdownToggle1">
                    <span class="passengerDetails"><i class="fa-solid fa-person-walking-luggage"></i> 1 Adult</span>
                    <!-- <i class="fa-solid fa-chevron-down"></i> -->
                    </div>
                    <div class="dropdown-menu" id="dropdownMenu1">
                    <div class="dropdown-item quantity" id="flightAdults">
                        <span>Adults</span>
                        <div>
                        <button class="flightDecrement">-</button>
                        <span class="count">1</span>
                        <button class="flightIncrement">+</button>
                        </div>
                    </div>
                    <div class="dropdown-item quantity" id="flightChildren">
                        <span>Children</span>
                        <div>
                        <button class="flightDecrement">-</button>
                        <span class="count">0</span>
                        <button class="flightIncrement">+</button>
                        </div>
                    </div>
                    <div class="dropdown-item quantity" id="flightInfants">
                        <span>Infants</span>
                        <div>
                        <button class="flightDecrement">-</button>
                        <span class="count">0</span>
                        <button class="flightIncrement">+</button>
                        </div>
                    </div>
                    <p id="flight-error-message" class="error-limit flightPessangerError"></p>
                    </div>
                </div>
        
                <!-- Economy -->
                <div class="dropdown">
                    <div class="dropdown-toggle" id="dropdownToggle2">
                        <span class="selected-country">Economy</span>
                    </div>
                    <div class="dropdown-menu economy-menu" id="dropdownMenu2">
                    <div class="dropdown-item">
                            <label>
                                <input type="radio" name="cabinClass" checked value="Y" onclick="updateSelection(this)"> Economy
                            </label>
                        </div>
                        <div class="dropdown-item">
                            <label>
                                <input type="radio" name="cabinClass" value="W" onclick="updateSelection(this)"> Premium Economy
                            </label>
                        </div>
                        <div class="dropdown-item">
                            <label>
                                <input type="radio" name="cabinClass" value="C" onclick="updateSelection(this)"> Business
                            </label>
                        </div>
                        <div class="dropdown-item">
                            <label>
                                <input type="radio" name="cabinClass" value="P" onclick="updateSelection(this)"> First
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fly">
            <ul>
                <li>
                    <a href="#">
                        <div class="select2-icon-wrapper">
                            <i class="fa-solid fa-location-dot select2-inner-icon"></i>
                            <select id="from" class=" select2" data-placeholder="Select Departure City"></select>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="mob-hid">
                        <i class="fa-solid fa-right-left"></i>
                    </div>
                </li>
                <li>
                    <a href="#">
                        <div class="select2-icon-wrapper">
                            <i class="fa-solid fa-location-dot select2-inner-icon"></i>
                            <select id="to" class="form-control select2" data-placeholder="Flying To (City or Airport)"></select>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="calendar-container flys">
                        <!-- <label for="departure">Departure Date</label>
                        <input class="p-2 border-0 modern-calendar" type="date" id="departure" name="departure"> -->
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                            <input id="departure" name="departure" type="text" class="form-control" placeholder="Departure Date">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="calendar-container flys">
                        <!-- <label for="Return">Return Date</label>
                        <input class="p-2 border-0 modern-calendar" type="date" id="returnDate"> -->
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                            <input id="returnDate" name="return" type="text" class="form-control" placeholder="Return Date">
                        </div>
                    </div>  
                </li>
                <li>
                    <div class="search-container">
                        <a class="pointer" id="searchFlight"><i class="fa-solid fa-magnifying-glass"></i></a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!-- <div class="box-2 tab-content">

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
                        <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                    </span>
                </div>
                <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                    <div class="dropdown-item quantity" id="adults">
                        <span>Adults</span>
                        <button class="decrement">-</button>
                        <span class="count">1</span>
                        <button class="increment">+</button>
                    </div>
                    <div class="dropdown-item quantity" id="children">
                        <span>Children</span>
                        <button class="decrement">-</button>
                        <span class="count">0</span>
                        <button class="increment">+</button>
                    </div>
                    <div class="dropdown-item quantity" id="infants">
                        <span>Infants</span>
                        <button class="decrement">-</button>
                        <span class="count">0</span>
                        <button class="increment">+</button>
                    </div>
                    <p id="error-message" class="error-limit"></p>
                </div>
            </div>



      
            <div class="Economy">
                <div class="dropdown-toggle" id="dropdownToggle2" onclick="toggleDropdown()">
                    <span class="selected-country">Economy</span>
                </div>
                <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="economy" onclick="updateSelection(this)"> Economy
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="premium" onclick="updateSelection(this)"> Premium Economy
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="business" onclick="updateSelection(this)"> Business
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="first" onclick="updateSelection(this)"> First
                        </label>
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
                    <a href="{{ route('flights')}}"><i class="fa-solid fa-magnifying-glass"></i></a>
                    </div>
                </li>
            </ul>
        </div>
    </div> -->
    <!-- <div class="box-3  tab-content">
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
                        <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                    </span>
                </div>
                <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                    <div class="dropdown-item quantity" id="adults">
                        <span>Adults</span>
                        <button class="decrement">-</button>
                        <span class="count">1</span>
                        <button class="increment">+</button>
                    </div>
                    <div class="dropdown-item quantity" id="children">
                        <span>Children</span>
                        <button class="decrement">-</button>
                        <span class="count">0</span>
                        <button class="increment">+</button>
                    </div>
                    <div class="dropdown-item quantity" id="infants">
                        <span>Infants</span>
                        <button class="decrement">-</button>
                        <span class="count">0</span>
                        <button class="increment">+</button>
                    </div>
                    <p id="error-message" class="error-limit"></p>
                </div>
      
            <div class="Economy">
                <div class="dropdown-toggle" id="dropdownToggle2" onclick="toggleDropdown()">
                    <span class="selected-country">Economy</span>
                </div>
                <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="economy" onclick="updateSelection(this)"> Economy
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="premium" onclick="updateSelection(this)"> Premium Economy
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="business" onclick="updateSelection(this)"> Business
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="first" onclick="updateSelection(this)"> First
                        </label>
                    </div>
                </div>

            </div>

        </div>


        <div class="fly">
            <ul>

            
                <li>
                    <a href="#">
                    <div class="main-flex">
                        <div class="locs">
                            <div class="icon-head-loc">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="flys">
                                <p>Flying From (City or Airport)</p>
                            </div>
                        </div>
                    </div>
                    </a>
                </li>
                <li>
                    <div class="right-left mob-hid">
                    <i class="fa-solid fa-right-left"></i>
                    </div>
                </li>
                <li>
                    <a href="#">
                    <div class="main-flex">
                        <div class="locs">
                            <div class="icon-head-loc">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="flys flys2">
                                <p>Flying From (City or Airport)</p>
                            </div>
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
                    <a href="{{ route('flights')}}"><i class="fa-solid fa-magnifying-glass"></i></a>
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
                        <i class="fa-solid fa-person-walking-luggage"></i> 1 Adult
                    </span>
                </div>
                <div class="dropdown-menu dropdown-menu1" id="dropdownMenu1" style="display: none;">
                    <div class="dropdown-item quantity" id="adults">
                        <span>Adults</span>
                        <button class="decrement">-</button>
                        <span class="count">1</span>
                        <button class="increment">+</button>
                    </div>
                    <div class="dropdown-item quantity" id="children">
                        <span>Children</span>
                        <button class="decrement">-</button>
                        <span class="count">0</span>
                        <button class="increment">+</button>
                    </div>
                    <div class="dropdown-item quantity" id="infants">
                        <span>Infants</span>
                        <button class="decrement">-</button>
                        <span class="count">0</span>
                        <button class="increment">+</button>
                    </div>
                    <p id="error-message" class="error-limit"></p>
                </div>
            </div>




            <div class="Economy">
                <div class="dropdown-toggle" id="dropdownToggle2" onclick="toggleDropdown()">
                    <span class="selected-country">Economy</span>
                </div>
                <div class="dropdown-menu dropdown-menu2" id="dropdownMenu2" style="display: none;">
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="economy" onclick="updateSelection(this)"> Economy
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="premium" onclick="updateSelection(this)"> Premium Economy
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="business" onclick="updateSelection(this)"> Business
                        </label>
                    </div>
                    <div class="dropdown-item">
                        <label>
                            <input type="radio" name="class" value="first" onclick="updateSelection(this)"> First
                        </label>
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
                    <a href="{{ route('flights')}}"><i class="fa-solid fa-magnifying-glass"></i></a>
                    </div>
                </li>
            </ul>
        </div>
    </div> -->
</div>
<!-- <script>
    document.querySelectorAll('.dropdown-toggle').forEach((toggle) => {
    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const menu = toggle.nextElementSibling;
        document.querySelectorAll('.dropdown-menu').forEach((m) => {
        if (m !== menu) m.classList.remove('active');
        });
        menu.classList.toggle('active');
    });
    });

    document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu').forEach((m) => m.classList.remove('active'));
    });
</script>
<script>
    $(document).ready(function () {
    $(".select2").select2({
        theme: "default",
        placeholder: $(this).data("placeholder"),
        minimumResultsForSearch: 5,
        width: "100%",
    });
    });
</script>
<script>

  // Dual-month date pickers
  flatpickr("#depart", {
    dateFormat: "d M Y",
    minDate: "today",
    onChange: function(selectedDates, dateStr) {
      if (selectedDates.length > 0) {
        returnPicker.set('minDate', selectedDates[0]);
      }
    }
  });

  const returnPicker = flatpickr("#return", {
    dateFormat: "d M Y",
    minDate: "today"
  });

  flatpickr("#tourStart", { dateFormat: "d M Y", minDate: "today" });
  flatpickr("#tourEnd", { dateFormat: "d M Y", minDate: "today" });
  flatpickr("#checkIn", { dateFormat: "d M Y", minDate: "today" });
  flatpickr("#checkOut", { dateFormat: "d M Y", minDate: "today" });
  flatpickr("#visaDate", { dateFormat: "d M Y", minDate: "today" });


</script>
<script>
    document.querySelectorAll('.input-group-text').forEach(icon => {
    icon.addEventListener('click', function() {
        const input = this.previousElementSibling;
        if (input) input.focus();
    });
    });
</script>
<script>
    const staticAirports = [
        @foreach($airports as $airport)
            { id: '{{ $airport->code }}', text: '{{ addslashes($airport->name) }} ({{ $airport->code }})' },
        @endforeach
    ];

    function setupAirportSelect(selector) {
        $(selector).select2({
            theme: 'classic',
            placeholder: $(selector).data('placeholder'),
            minimumInputLength: 0,
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    
                    if (!term.length) {
                        // No search term, show only static list
                        success({ results: staticAirports });
                        return;
                    }

                    // With search term, show only remote results
                    $.ajax({
                        url: '{{ route("airport") }}',
                        dataType: 'json',
                        delay: 250,
                        data: { term },
                        success: function (data) {
                            success({ results: data.results });
                        },
                        error: failure
                    });
                },
                processResults: function (data) {
                    return data;
                },
                cache: true
            }
        });
    }
    const setInitialAirportValue = (selector, code) => {
        if (!code) return;

        const staticMatch = staticAirports.find(a => a.id === code);

        if (staticMatch) {
            const option = new Option(staticMatch.text, staticMatch.id, true, true);
            $(selector).append(option).trigger('change');
        } else {
            $.ajax({
                url: '{{ route("airport") }}',
                data: { term: code },
                dataType: 'json',
                success: function (data) {
                    const match = data.results.find(item => item.id === code);
                    if (match) {
                        const option = new Option(match.text, match.id, true, true);
                        $(selector).append(option).trigger('change');
                    }
                }
            });
        }
    };


    document.getElementById('dropdownToggle1').addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent immediate closing
        const menu = document.getElementById('dropdownMenu1');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });
    $(document).ready(function () {
        const getURLParam = param => new URLSearchParams(window.location.search).get(param) || "";
        
        let departure = $("#departure");
        let returnDate = $("#returnDate");

        let today = new Date().toISOString().split('T')[0];
        departure.attr("min", today)

        departure.val(getURLParam("dep"));
        returnDate.val(getURLParam("return"));

        returnDate.attr("min", departure.val() || today)
        departure.on("change", function () {
            let selectedDeparture = $(this).val();
            returnDate.attr("min", selectedDeparture);

            // clear returnDate if it's before the new departure date
            if (returnDate.val() < selectedDeparture) {
                returnDate.val("");
            }
        });
        // $("#from").val(getURLParam("arr"));
        // $("#to").val(getURLParam("dest"));
        setInitialAirportValue('#from', getURLParam("arr"));
        setInitialAirportValue('#to', getURLParam("dest"));


        if(!$("#returnDate").val()){
            $("#returnDate").prop("disabled", true);
            $("#oneWaySearch").prop("checked", true);
        } else {
            $("#returnSearch").prop("checked", true);
        }

        $('#oneWaySearch').change(function() {
            $("#returnDate").prop("disabled", this.checked);
            $("#returnDate").val(null);
        });
        $('#returnSearch').change(function() {
            $("#returnDate").removeProp("disabled", this.checked);
            $("#returnDate").val(getURLParam("return"));
        });

        let adults = parseInt(getURLParam("adt")) || 1;
        let children = parseInt(getURLParam("chd")) || 0;
        let infants = parseInt(getURLParam("inf")) || 0;

        $("#flightAdults .count").text(adults);
        $("#flightChildren .count").text(children);
        $("#flightInfants .count").text(infants);

        const updatePassengerSummary = () => {
            let totalPassengers = adults + children + infants;
            $(".passengerDetails").html(`<i class="fa-solid fa-person-walking-luggage"></i> ${totalPassengers} Passenger${totalPassengers > 1 ? "s" : ""}`);
        };

        const validatePassengerCounts = () => {
            let totalPassengers = adults + children + infants;
            let errorMsg = "";

            if (infants > adults) {
                errorMsg = "Infants cannot exceed the number of adults.";
            } else if (totalPassengers > 9) {
                errorMsg = "Total passengers cannot be more than 9.";
            }
            $(".flightPessangerError").text(errorMsg);
            return errorMsg === "";
        };

        $(".flightIncrement, .flightDecrement").click(function () {
            let parent = $(this).closest(".quantity");
            let countSpan = parent.find(".count");
            let isIncrement = $(this).hasClass("flightIncrement");

            let totalPassengers = adults + children + infants;

            if (isIncrement) {
                if (totalPassengers >= 9) {
                    $(".flightPessangerError").text("Total passengers cannot be more than 9.");
                    return;
                }

                if (parent.attr("id") === "flightAdults") adults++;
                else if (parent.attr("id") === "flightChildren") children++;
                else if (parent.attr("id") === "flightInfants") {
                    if (infants < adults) infants++;
                    else {
                        $(".flightPessangerError").text("Infants cannot exceed the number of adults.");
                        return;
                    }
                }
            } else {
                if (parent.attr("id") === "flightAdults") adults = Math.max(adults - 1, 1);
                else if (parent.attr("id") === "flightChildren") children = Math.max(children - 1, 0);
                else if (parent.attr("id") === "flightInfants") infants = Math.max(infants - 1, 0);
            }

            $("#flightAdults .count").text(adults);
            $("#flightChildren .count").text(children);
            $("#flightInfants .count").text(infants);

            validatePassengerCounts();
            updatePassengerSummary();
        });

        updatePassengerSummary();

        $("#searchFlight").click(function (event) {
            event.preventDefault();
            
            let cabinClass = $('input[name="cabinClass"]:checked').val();
            let from = $('#from').val();
            let destination = $('#to').val();
            let departure = $("#departure").val();
            let returnDate = $("#returnDate").val();

            if (!from || !destination || !departure) return _alert("Please fill all required fields.", 'warning')

            if (!validatePassengerCounts()) return;

            window.location.href = `/flights?arr=${from}&dest=${destination}&dep=${departure}&return=${returnDate}&cabinClass=${cabinClass}&adt=${adults}&chd=${children}&inf=${infants}`;
        });
        setupAirportSelect('#from');
        setupAirportSelect('#to');
    });
</script> -->
<!-- citys -->




<script>
/* ==========================
   DROPDOWN MENU HANDLER
========================== */
document.querySelectorAll('.dropdown-toggle').forEach((toggle) => {
  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    const menu = toggle.nextElementSibling;
    document.querySelectorAll('.dropdown-menu').forEach((m) => {
      if (m !== menu) m.classList.remove('active');
    });
    menu.classList.toggle('active');
  });
});

document.addEventListener('click', () => {
  document.querySelectorAll('.dropdown-menu').forEach((m) => m.classList.remove('active'));
});
</script>

<script>
/* ==========================
   SELECT2 INITIALIZATION
========================== */
$(document).ready(function () {
  $(".select2").select2({
    theme: "default",
    placeholder: function() {
      return $(this).data("placeholder");
    },
    minimumResultsForSearch: 5,
    width: "100%",
  });
});
</script>

<script>
/* ==========================
   FLATPICKR INITIALIZATION
========================== */
const returnPicker = flatpickr("#returnDate", {
  dateFormat: "d M Y",
  minDate: "today"
});

flatpickr("#departure", {
  dateFormat: "d M Y",
  minDate: "today",
  onChange: function(selectedDates) {
    if (selectedDates.length > 0) {
      returnPicker.set('minDate', selectedDates[0]);
    }
  }
});

flatpickr("#tourStart", { dateFormat: "d M Y", minDate: "today" });
flatpickr("#tourEnd", { dateFormat: "d M Y", minDate: "today" });
flatpickr("#checkIn", { dateFormat: "d M Y", minDate: "today" });
flatpickr("#checkOut", { dateFormat: "d M Y", minDate: "today" });
flatpickr("#visaDate", { dateFormat: "d M Y", minDate: "today" });
</script>

<script>
/* ==========================
   INPUT FOCUS ON ICON CLICK
========================== */
document.querySelectorAll('.input-group-text').forEach(icon => {
  icon.addEventListener('click', function() {
    const input = this.previousElementSibling;
    if (input) input.focus();
  });
});
</script>

<script>
/* ==========================
   AIRPORT SELECT2 WITH STATIC + AJAX
========================== */
const staticAirports = [
  @foreach($airports as $airport)
    { id: '{{ $airport->code }}', text: '{{ addslashes($airport->name) }} ({{ $airport->code }})' },
  @endforeach
];

function setupAirportSelect(selector) {
  $(selector).select2({
    theme: 'classic',
    placeholder: $(selector).data('placeholder'),
    minimumInputLength: 0,
    ajax: {
      transport: function (params, success, failure) {
        const term = params.data.term || '';

        if (!term.length) {
          success({ results: staticAirports });
          return;
        }

        $.ajax({
          url: '{{ route("airport") }}',
          dataType: 'json',
          delay: 250,
          data: { term },
          success: function (data) {
            success({ results: data.results });
          },
          error: failure
        });
      },
      processResults: function (data) {
        return data;
      },
      cache: true
    }
  });
}

function setInitialAirportValue(selector, code) {
  if (!code) return;

  const staticMatch = staticAirports.find(a => a.id === code);

  if (staticMatch) {
    const option = new Option(staticMatch.text, staticMatch.id, true, true);
    $(selector).append(option).trigger('change');
  } else {
    $.ajax({
      url: '{{ route("airport") }}',
      data: { term: code },
      dataType: 'json',
      success: function (data) {
        const match = data.results.find(item => item.id === code);
        if (match) {
          const option = new Option(match.text, match.id, true, true);
          $(selector).append(option).trigger('change');
        }
      }
    });
  }
}
</script>

<script>
/* ==========================
   FLIGHT SEARCH LOGIC
========================== */
$(document).ready(function () {
  const getURLParam = (param) => new URLSearchParams(window.location.search).get(param) || "";

   const departurePicker = flatpickr("#departure", {
    dateFormat: "d M Y",
    minDate: "today",
    onChange: function(selectedDates) {
      if (selectedDates.length > 0) {
        returnPicker.set('minDate', selectedDates[0]);
      }
    }
  });

  const returnPicker = flatpickr("#returnDate", {
    dateFormat: "d M Y",
    minDate: "today"
  });

  // Helper to parse ISO to Flatpickr format
  function setPickerFromISO(picker, isoDate) {
    if (!isoDate) return;
    const date = new Date(isoDate);
    if (!isNaN(date)) {
      picker.setDate(date, true); // true triggers onChange
    }
  }

  // Set dates from URL params
  setPickerFromISO(departurePicker, getURLParam("dep"));
  setPickerFromISO(returnPicker, getURLParam("return"));
//   let departure = $("#departure");
//   let returnDate = $("#returnDate");

//   let today = new Date().toISOString().split('T')[0];
//   departure.attr("min", today);

//   departure.val(getURLParam("dep"));
//   returnDate.val(getURLParam("return"));

//   returnDate.attr("min", departure.val() || today);

//   departure.on("change", function () {
//     let selectedDeparture = $(this).val();
//     returnDate.attr("min", selectedDeparture);

//     if (returnDate.val() < selectedDeparture) {
//       returnDate.val("");
//     }
//   });

  setInitialAirportValue('#from', getURLParam("arr"));
  setInitialAirportValue('#to', getURLParam("dest"));

  if (!$("#returnDate").val()) {
    $("#returnDate").prop("disabled", true);
    $("#oneWaySearch").prop("checked", true);
  } else {
    $("#returnSearch").prop("checked", true);
  }

  $('#oneWaySearch').change(function() {
    $("#returnDate").prop("disabled", this.checked).val(null);
  });

  $('#returnSearch').change(function() {
    $("#returnDate").prop("disabled", !this.checked);
    $("#returnDate").val(getURLParam("return"));
  });

  let adults = parseInt(getURLParam("adt")) || 1;
  let children = parseInt(getURLParam("chd")) || 0;
  let infants = parseInt(getURLParam("inf")) || 0;

  $("#flightAdults .count").text(adults);
  $("#flightChildren .count").text(children);
  $("#flightInfants .count").text(infants);

//   const updatePassengerSummary = () => {
//     let totalPassengers = adults + children + infants;
//     $(".passengerDetails").html(`<i class="fa-solid fa-person-walking-luggage"></i> ${totalPassengers} Passenger${totalPassengers > 1 ? "s" : ""}`);
//   };

const updatePassengerSummary = () => {
    let totalPassengers = adults + children + infants;
    $(".passengerDetails").html(`
        <i class="fa-solid fa-person-walking-luggage"></i> 
        ${totalPassengers} <span class="passenger-text">Passenger${totalPassengers > 1 ? "s" : ""}</span>
    `);
};

  const validatePassengerCounts = () => {
    let totalPassengers = adults + children + infants;
    let errorMsg = "";

    if (infants > adults) {
      errorMsg = "Infants cannot exceed the number of adults.";
    } else if (totalPassengers > 9) {
      errorMsg = "Total passengers cannot be more than 9.";
    }
    $(".flightPessangerError").text(errorMsg);
    return errorMsg === "";
  };

  $(".flightIncrement, .flightDecrement").click(function () {
    let parent = $(this).closest(".quantity");
    let isIncrement = $(this).hasClass("flightIncrement");
    let totalPassengers = adults + children + infants;

    if (isIncrement) {
      if (totalPassengers >= 9) {
        $(".flightPessangerError").text("Total passengers cannot be more than 9.");
        return;
      }

      if (parent.attr("id") === "flightAdults") adults++;
      else if (parent.attr("id") === "flightChildren") children++;
      else if (parent.attr("id") === "flightInfants") {
        if (infants < adults) infants++;
        else {
          $(".flightPessangerError").text("Infants cannot exceed the number of adults.");
          return;
        }
      }
    } else {
      if (parent.attr("id") === "flightAdults") adults = Math.max(adults - 1, 1);
      else if (parent.attr("id") === "flightChildren") children = Math.max(children - 1, 0);
      else if (parent.attr("id") === "flightInfants") infants = Math.max(infants - 1, 0);
    }

    $("#flightAdults .count").text(adults);
    $("#flightChildren .count").text(children);
    $("#flightInfants .count").text(infants);

    validatePassengerCounts();
    updatePassengerSummary();
  });

  updatePassengerSummary();

  $("#searchFlight").click(function (event) {
    event.preventDefault();

    let cabinClass = $('input[name="cabinClass"]:checked').val();
    let from = $('#from').val();
    let destination = $('#to').val();
    let departureDate = formatDateToISO($("#departure").val());
    let returnRaw = $("#returnDate").val();
    let returnDateVal = returnRaw && returnRaw !== "null" ? formatDateToISO(returnRaw) : null;
    console.log(from, destination, departureDate, returnDateVal);
    // return

    if (!from || !destination || !departureDate) {
      alert("Please fill all required fields.");
      return;
    }

    if (!validatePassengerCounts()) return;

    let url = `/flights?arr=${from}&dest=${destination}&dep=${departureDate}`;
    if (returnDateVal) url += `&return=${returnDateVal}`;
    url += `&cabinClass=${cabinClass}&adt=${adults}&chd=${children}&inf=${infants}`;

    window.location.href = url;
  });

  setupAirportSelect('#from');
  setupAirportSelect('#to');
});

function formatDateToISO(dateStr) {
    if (!dateStr) return null;
    // Parse the date string
    const date = new Date(dateStr);
    
    // Check if date is valid
    if (isNaN(date)) return null;

    // Get components
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // months are 0-based
    const day = date.getDate().toString().padStart(2, '0');

    // Return in YYYY-MM-DD format
    return `${year}-${month}-${day}`;
}
function formatISOToReadable(dateStr) {
    if (!dateStr) return null;
    const date = new Date(dateStr);
    if (isNaN(date)) return null;
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    return `${day} ${month} ${year}`;
}
</script>




{{-- <script>
    $(document).ready(function() {
        $('.select2').select2(); // Initialize Select2 for all elements with the class 'select2'
    });




    $(document).ready(function () {
        // Initialize select2
        $('.select2').select2();

        // Jab "from" city select ho to "to" field open ho
        $('#from').on('select2:select', function () {
            $('#to').select2('open');
        });

        // Jab "to" city select ho to "departure" par focus ho
        $('#to').on('select2:select', function () {
            $('#departure').focus();
        });

        // Jab "departure" date select ho to "returnDate" par focus ho
        $('#departure').on('change', function () {
            $('#returnDate').focus();
        });
    });

    jQuery(document).ready(function($) {
        var docBody = $(document.body);
        var shiftPressed = false;
        var clickedOutside = false;
        //var keyPressed = 0;

        docBody.on('keydown', function(e) {
            var keyCaptured = (e.keyCode ? e.keyCode : e.which);
            //shiftPressed = keyCaptured == 16 ? true : false;
            if (keyCaptured == 16) { shiftPressed = true; }
        });
        docBody.on('keyup', function(e) {
            var keyCaptured = (e.keyCode ? e.keyCode : e.which);
            //shiftPressed = keyCaptured == 16 ? true : false;
            if (keyCaptured == 16) { shiftPressed = false; }
        });

        docBody.on('mousedown', function(e){
            // remove other focused references
            clickedOutside = false;
            // record focus
            if ($(e.target).is('[class*="select2"]')!=true) {
                clickedOutside = true;
            }
        });

        docBody.on('select2:opening', function(e) {
            // this element has focus, remove other flags
            clickedOutside = false;
            // flag this Select2 as open
            $(e.target).attr('data-s2open', 1);
        });
        docBody.on('select2:closing', function(e) {
            // remove flag as Select2 is now closed
            $(e.target).removeAttr('data-s2open');
        });

        docBody.on('select2:close', function(e) {

            var elSelect = $(e.target);
            elSelect.removeAttr('data-s2open');
            var currentForm = elSelect.closest('form');
        
            var othersOpen = currentForm.has('[data-s2open]').length;
            if (othersOpen == 0 && clickedOutside==false) {
                /* Find all inputs on the current form that would normally not be focus`able:
                *  - includes hidden <select> elements whose parents are visible (Select2)
                *  - EXCLUDES hidden <input>, hidden <button>, and hidden <textarea> elements
                *  - EXCLUDES disabled inputs
                *  - EXCLUDES read-only inputs
                */
                var inputs = currentForm.find(':input:enabled:not([readonly], input:hidden, button:hidden, textarea:hidden)')
                    .not(function () {   // do not include inputs with hidden parents
                        return $(this).parent().is(':hidden');
                    });
                var elFocus = null;
                $.each(inputs, function (index) {
                    var elInput = $(this);

                    if (elInput.attr('id') == elSelect.attr('id')) {
                        if ( shiftPressed) { // Shift+Tab
                            elFocus = inputs.eq(index - 1);

                        } else {
                            elFocus = inputs.eq(index + 1);

                        }
                        return false;
                    }
                });
                if (elFocus !== null) {
                    // automatically move focus to the next field on the form
                    var isSelect2 = elFocus.siblings('.select2').length > 0;
                    if (isSelect2) {
                        elFocus.select2('open');
                    } else {
                        elFocus.focus();
                    }
                }
            }
        });

        docBody.on('focus', '.select2', function(e) {

            var elSelect = $(this).siblings('select');
            if (elSelect.is('[disabled]')==false && elSelect.is('[data-s2open]')==false
                && $(this).has('.select2-selection--single').length>0) {
                elSelect.attr('data-s2open', 1);
                elSelect.select2('open');
            }
        });

    });
</script> --}}
