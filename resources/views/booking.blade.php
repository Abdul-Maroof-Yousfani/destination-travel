<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>Flights</title>
    <style>
        .pointer {
            cursor: pointer;
        }
    </style>
</head>
<body>
    {{-- @foreach($flightDetails['OriginDestinationOptions'] as $option)
        @dd($option['OriginDestinationOption'])
    @endforeach --}}
    @php
        $passengerTypes = [
            'adt' => 'Adult',
            'chd' => 'Child',
            'inf' => 'Infant'
        ];
    @endphp
    @foreach ($passengerTypes as $key => $type)
        @if(isset($data['paxCount'][$key]) && $data['paxCount'][$key] > 0)
            <div class="row m-3 paxDetails">
                <h1>{{ $type }}</h1>
                @for ($i = 1; $i <= $data['paxCount'][$key]; $i++)
                    <div class="loop border border-3 p-3 col-4">
                        <h3>{{ $type }} {{ $i }}</h3>
                        <label for="name">Enter Name</label>
                        <input type="text" name="{{ $key }}_name[]" required value="Alii_{{ $key }}{{ $i }}">
                        <br><br>

                        <label for="surname">Enter Surname</label>
                        <input type="text" name="{{ $key }}_surname[]" required value="Syed_{{ $key }}{{ $i }}">
                        <br><br>

                        <label for="nameTitle">Name Title</label>
                        <select name="{{ $key }}_title[]" required>
                            <option value="" disabled selected>Select Title</option>
                            <option value="Mr" selected>Mr</option>
                            <option value="Mrs">Mrs</option>
                            <option value="Miss">Miss</option>
                        </select>
                        <br><br>

                        <label for="dob">Date of Birth</label>
                        <input type="date" name="{{ $key }}_dob[]" required>
                        <br><br>

                        <label for="areaCode">Enter Area Code</label>
                        <input type="number" name="{{ $key }}_area_code[]" required value="06">
                        <br><br>

                        <label for="countryPhoneCode">Enter Country Phone Code</label>
                        <input type="number" name="{{ $key }}_phone_code[]" required value="92">
                        <br><br>

                        <label for="phone">Enter Phone</label>
                        <input type="number" name="{{ $key }}_phone[]" required value="131385465132">
                        <br><br>

                        <label for="countrycode">Enter Country Code</label>
                        <input type="text" name="{{ $key }}_country_code[]" required value="PK">
                    </div>
                @endfor
            </div>
        @endif
    @endforeach
    <div class="btn btn-primary m-3 w-25" id="submitBtn">Continue</div>

    <div class="modal fade" id="ticketModal" aria-hidden="true" aria-labelledby="ticketModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="ticketModalLabel">Ticket details</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="d-flex align-items-center gap-3 mx-3">
                <p class="fs-3">Booking Reference ID</p>
                <span id="refId"></span>
            </div>
            <div class="modal-body contactDetails">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <button class="btn btn-primary" data-bs-target="#ticketModal" data-bs-toggle="modal">Show Again</button>
<script>
    let data = @json($data);
    $('#submitBtn').click(function() {
        _loader('show');
        let passengers = [];

        $('.paxDetails').each(function() {
            let type = $(this).find('h1').text().trim();
            $(this).find('.loop').each(function(index) {
                let passenger = {
                    type: type,
                    name: $(this).find('input[name$="_name[]"]').val(),
                    surname: $(this).find('input[name$="_surname[]"]').val(),
                    title: $(this).find('select[name$="_title[]"]').val(),
                    dob: $(this).find('input[name$="_dob[]"]').val(),
                    areaCode: $(this).find('input[name$="_area_code[]"]').val(),
                    phoneCode: $(this).find('input[name$="_phone_code[]"]').val(),
                    phone: $(this).find('input[name$="_phone[]"]').val(),
                    countryCode: $(this).find('input[name$="_country_code[]"]').val()
                    nationality: $(this).find('input[name$="_country_code[]"]').val()
                };
                passengers.push(passenger);
            });
        });
        $.post({
            url: "{{route('demoBookFlight')}}",
            data: {
                passengers,
                data,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                $("#ticketModal").modal("show");
                $("#refId").text(response.bookingRefID);
                $(".contactDetails").html(renderCards(response.data));
            },
            error: function (xhr) {
                alert(xhr.responseJSON.message);
            },
            complete: function () {
                _loader('hide');
            }
        })
    });

    const renderCards = data => {
        if (!Array.isArray(data) || data.length === 0) {
            return `<div class="alert alert-danger" role="alert">Data is missing :)</div>`;
        }
        return data.map(row => {
            return `
                <div class="card m-3">
                    <div class="card-body">
                        <table class="table">
                            <thead class="thead-dark mx-3">
                                <tr class="d-flex gap-3">
                                    <th class="badge text-bg-primary">Contact Details</th>
                                    <th class="badge text-bg-primary">${row.type}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Name</strong></td>
                                    <td>${row.name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Surname</strong></td>
                                    <td>${row.surName}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone number</strong></td>
                                    <td>${row.phoneNumber}</td>
                                </tr>
                                <tr>
                                    <td><strong>Traveler Reference Number</strong></td>
                                    <td>${row.travelerRefNumber}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>`;
        }).join('');
    };

    const _loader = (action) => {
        if (action === 'show') {
            $("body").append(`
                <div id="loader" class="w-100 bg-dark vh-100 bg-opacity-25 position-fixed top-0 z_inf">
                    <div class="position-relative top-50 start-50 spinner-border text-white"></div>
                </div>
            `);
        } else {
            $('#loader').remove();
        }
    };
</script>
</body>
</html>