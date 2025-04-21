
<label for="from">From:</label>
<input type="text" id="from" name="from" required />
<br><br>

<label for="to">To:</label>
<input type="text" id="to" name="to" required />
<br><br>

<label for="departure">Departure Date:</label>
<input type="date" id="departure" name="departureDate" required />
<br><br>

<label for="returnDate">Return Date:</label>
<input type="date" id="returnDate" name="returnDate" />
<br><br>

<label for="adult">Adult:</label>
<input type="number" id="adult" name="adult" min="1" required />
<br><br>

<label for="child">Child:</label>
<input type="number" id="child" name="child" min="0" />
<br><br>

<label for="infant">Infant:</label>
<input type="number" id="infant" name="infant" min="0" />
<br><br>

<input type="submit" value="Search" id="searchFlight" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    const getURLParam = param => {
        let urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param) || "";
    }
    $("#from").val(getURLParam("arr"));
    $("#to").val(getURLParam("dest"));
    $("#departure").val(getURLParam("dep"));
    $("#returnDate").val(getURLParam("return"));
    $("#adult").val(getURLParam("adt") || 1);
    $("#child").val(getURLParam("chd") || 0);
    $("#infant").val(getURLParam("inf") || 0);

    $("#searchFlight").click(function (event) {
        event.preventDefault();

        let from = $("#from").val();
        let destination = $("#to").val();
        let departure = $("#departure").val();
        let returnDate = $("#returnDate").val();
        let adult = $("#adult").val() || 1;
        let child = $("#child").val() || 0;
        let infant = $("#infant").val() || 0;

        if (!from || !destination || !departure) {
            alert("Please fill in all required fields.");
            return;
        }
        window.location.href = `/demo/flights/search?arr=${from}&dest=${destination}&dep=${departure}&return=${returnDate}&adt=${adult}&chd=${child}&inf=${infant}`;
    });
});
</script>
