<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <h1>Search Flight</h1>
    <x-searchflightold/>
    {{-- <form action="{{route('flights.search')}}" method="post">
        @csrf
        <label for="from">From:</label>
        <input type="text" id="from" name="from" required />
        <br><br>
        <label for="to">To:</label>
        <input type="text" id="to" name="to" required />
        <br>
        <br>
        <label for="departure">Departure Date:</label>
        <input type="date" id="departure" name="departureDate" required />
        <br>
        <br>
        <label for="return">Return Date:</label>
        <input type="date" id="return" name="returnDate" />
        <br>
        <br>
        <label for="adult">Adult:</label>
        <input type="number" id="adult" name="adult" />
        <br>
        <br>
        <label for="child">Child:</label>
        <input type="number" id="child" name="child" />
        <br>
        <br>
        <label for="infant">Infant:</label>
        <input type="number" id="infant" name="infant" />
        <br>
        <br>
        <br>
        <input type="submit" value="Search" />
    </form> --}}
    {{-- <input type="radio" id="oneWay" name="tripType" value="oneWay" checked />
    <label for="oneWay">One Way</label>
    <input type="radio" id="return" name="tripType" value="return" />
    <label for="return">Return</label>
    <input type="radio" id="multiCity" name="tripType" value="multiCity" />
    <label for="multiCity">Multi-City</label>
    <input type="radio" id="flexible" name="tripType" value="flexible" />
    <label for="flexible">Flexible</label>
    <input type="radio" id="premium" name="tripType" value="premium" />
    <label for="premium">Premium</label>
    <input type="radio" id="business" name="tripType" value="business" />
    <label for="business">Business</label>
    <input type="radio" id="economy" name="tripType" value="economy" />
    <label for="economy">Economy</label>
    <input type="radio" id="firstClass" name="tripType" value="firstClass" />
    <label for="firstClass">First Class</label>
    <input type="radio" id="lastMinute" name="tripType" value="lastMinute" />
    <label for="lastMinute">Last Minute</label>
    <input type="radio" id="direct" name="tripType" value="direct" />
    <label for="direct">Direct</label>
    <input type="radio" id="stopover" name="tripType" value="stopover" />
    <label for="stopover">Stopover</label> --}}
</body>
</html>