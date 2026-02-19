@extends('admin.layouts.master')

@section('title', 'Settings')

@section('style')
<style>
.available-date a {
    background-color: var(--primary-color) !important;
    color: #fff !important;
}
.unavailable-date a {
    color: #ccc !important;
}
</style>
@endsection

@section('content')
<div class="row">
    <h2 class="fw-bold mb-4">Settings</h2>
</div>
<div class="row justify-content-between h-100">

    @can('download logs')
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg_primary text-white">
                    <h4 class="mb-0">Download Airline Logs</h4>
                </div>
                <div class="card-body">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Select Airline</label>
                            <select id="airlineSelect" class="form-select" required>
                                <option value="">-- Select Airline --</option>
                                @foreach($airlines as $airline)
                                    <option value="{{ $airline }}">{{ ucfirst($airline) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Available Log Dates</label>
                            <input type="text" id="logCalendar" class="form-control" placeholder="Select date" readonly>
                        </div>
                    </div>

                    <div id="logInfo" class="mt-4" style="display:none;">
                        <h5>Files for <span id="selectedDate"></span></h5>
                        <div id="fileButtons" class="mt-3 d-flex gap-3"></div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    @can('manage airports')
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg_primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Manage Airports</h4>
                    <button class="btn btn-light btn-sm" id="addAirportBtn">+ Add Airport</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Search Airport</label>
                        <select id="airportSearch" class="form-select" data-placeholder="Search airport name or code"></select>
                    </div>

                    <table class="table table-striped table-hover" id="airportTable">
                        <thead class="table-light">
                            <tr>
                                <th>Order By</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Country</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody><tr><td colspan="5" class="text-center text-muted">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    @endcan

    <!-- HOTEL Modal -->
    <div class="modal fade" id="airportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg_primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add Airport</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="airportForm">
                        @csrf
                        <input type="hidden" id="airport_id">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Order By</label>
                                <input type="number" id="order_by" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Code</label>
                                <input type="text" id="code" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Country</label>
                                <input type="text" id="country" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Time Zone</label>
                                <input type="text" id="time_zone" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City Code</label>
                                <input type="text" id="city_code" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Airport Type</label>
                                <select name="is_local" id="is_local" class="form-control">
                                    <option selected value="0">International</option>
                                    <option value="1">Domestic</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <input type="text" id="city" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <input type="text" id="state" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">County</label>
                                <input type="text" id="county" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="saveAirportBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
    @can('manage setting')
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg_primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Manage Country Hotels</h4>
                    <button class="btn btn-light btn-sm" id="fetchCountryInfoBtn">+ Fetch TassPro Info</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Search Hotel</label>
                        <select id="hotelSearch" class="form-select" data-placeholder="Search city or code"></select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="hotelTable">
                            <thead class="table-light">
                                <tr>
                                    <th>City</th>
                                    <th>Code</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($countryHotels as $hotel)
                                <tr>
                                    <td>{{ $hotel->city }}</td>
                                    <td>{{ $hotel->destinationcode }}</td>
                                    <td>{{ $hotel->country }}</td>
                                    <td>
                                        @if($hotel->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning editHotelBtn" data-id="{{ $hotel->id }}">Edit</button>
                                        <button class="btn btn-sm btn-danger deleteHotelBtn" data-id="{{ $hotel->id }}">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                                @if($countryHotels->isEmpty())
                                <tr><td colspan="5" class="text-center text-muted">No hotels found</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <div class="modal fade" id="fetchHotelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg_primary text-white">
                    <h5 class="modal-title">Fetch TassPro Country Info</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Country Code</label>
                            <input type="text" id="fetch_country_code" class="form-control" placeholder="e.g. IN">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="btnPreviewInfo">Fetch Preview</button>
                        </div>
                    </div>
                    
                    <div id="previewContainer" style="display:none;">
                        <h5>Preview: <span id="previewCount"></span> cities found</h5>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code (Key)</th>
                                        <th>City (Value)</th>
                                    </tr>
                                </thead>
                                <tbody id="previewBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="confirmFetchBtn" style="display:none;">Confirm & Insert</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editHotelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg_primary text-white">
                    <h5 class="modal-title">Edit Country Hotel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editHotelForm">
                        @csrf
                        <input type="hidden" id="edit_hotel_id">
                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <input type="text" id="edit_city" name="city" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Destination Code</label>
                            <input type="text" id="edit_destinationcode" name="destinationcode" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" id="edit_country" name="country" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nationality</label>
                            <input type="text" id="edit_nationality" name="nationality" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="edit_status" name="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="updateHotelBtn">Update</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function(){
    // ------------------------ Airline Management ------------------------
    let availableDates = {};

    $("#logCalendar").datepicker({
        dateFormat: "yy-mm-dd",
        beforeShowDay: function(date) {
            const d = $.datepicker.formatDate('yy-mm-dd', date);
            const available = availableDates.hasOwnProperty(d);
            return [available, available ? "available-date" : "unavailable-date"];
        },
        onSelect: function(dateText) {
            $('#selectedDate').text(dateText);
            const files = availableDates[dateText] || {};
            const airline = $('#airlineSelect').val();
            let html = '';

            // Airline log
            if (files.log) {
                html += `
                    <a href="{{ route('admin.logs.download') }}?airline=${encodeURIComponent(airline)}&file=${encodeURIComponent(files.log)}"
                        class="btn btn-success">🧾 Download Airline Log</a>`;
            } else {
                html += `<button class="btn btn-secondary" disabled>🧾 Airline Log Not Found</button>`;
            }

            // Booking log
            if (files.booking) {
                html += `
                    <a href="{{ route('admin.logs.download') }}?airline=${encodeURIComponent(airline)}&file=${encodeURIComponent(files.booking)}"
                        class="btn btn-info">📘 Download Booking Log</a>`;
            } else {
                html += `<button class="btn btn-secondary" disabled>📘 Booking Log Not Found</button>`;
            }

            $('#fileButtons').html(html);
            $('#logInfo').show();
        }
    });

    $('#airlineSelect').on('change', function(){
        const airline = $(this).val();
        $('#logInfo').hide();

        if (!airline) {
            availableDates = {};
            $("#logCalendar").datepicker("refresh");
            return;
        }

        $.ajax({
            url: "{{ route('admin.logs.dates') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", airline: airline },
            success: function(response){
                availableDates = response.availableDates;
                $("#logCalendar").datepicker("refresh");
            },
            error: function(){
                _alert('Error loading available dates.', 'error');
            }
        });
    });




    // ------------------------ Airport Management ------------------------
    const modal = new bootstrap.Modal(document.getElementById('airportModal'));
    const tableBody = $('#airportTable tbody');
    let editMode = false;
    let selectedId = null;

    loadAirports();

    function loadAirports() {
        $.get('{{ route("admin.airports.list") }}', function(data){
            if (!data.length) {
                tableBody.html('<tr><td colspan="5" class="text-center text-muted">No airports found</td></tr>');
                return;
            }
            let html = '';
            data.forEach(a => {
                html += `
                <tr>
                    <td>${a.order_by}</td>
                    <td>${a.name}</td>
                    <td>${a.code}</td>
                    <td>${a.country || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editBtn" data-id="${a.id}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${a.id}">Delete</button>
                    </td>
                </tr>`;
            });
            tableBody.html(html);
        });
    }

    $('#airportSearch').select2({
        theme: 'classic',
        placeholder: $('#airportSearch').data('placeholder'),
        ajax: {
            url: '{{ route("airport") }}',
            dataType: 'json',
            delay: 300,
            data: params => ({ term: params.term }),
            processResults: data => ({ results: data.results }),
        }
    }).on('select2:select', function (e) {
        const code = e.params.data.id;

        $.get('{{ route("admin.airports.single") }}', { code }, function (airport) {
            if (airport && airport.id) {
                openModal(airport);
                $('#airportSearch').val(null).trigger('change');
            } else {
                _alert('Airport not found in local DB', 'error');
            }
        }).fail(() => {
            _alert('Error fetching airport details.', 'error');
        });
    });


    $('#addAirportBtn').on('click', function(){
        $('#modalTitle').text('Add Airport');
        $('#airportForm')[0].reset();
        $('#airport_id').val('');
        editMode = false;
        modal.show();
    });

    $(document).on('click', '.editBtn', function(){
        const id = $(this).data('id');
        $.get('{{ route("admin.airports.show", ":id") }}'.replace(':id', id), function(airport){
            if (airport && airport.id) setTimeout(() => openModal(airport), 100);
            else _alert('Airport not found.', 'error');
        }).fail(() => _alert('Error fetching airport details.', 'error'));
    });

    function openModal(airport) {
        $('#airportForm')[0].reset();
        $('#airport_id').val('');

        $('#modalTitle').text('Edit Airport');
        $('#airport_id').val(airport.id);
        $('#name').val(airport.name);
        $('#order_by').val(airport.order_by);
        $('#code').val(airport.code);
        $('#country').val(airport.country);
        $('#time_zone').val(airport.time_zone);
        $('#city_code').val(airport.city_code);
        $('#is_local').val(airport.is_local ? '1' : '0'); 
        $('#city').val(airport.city);
        $('#state').val(airport.state);
        $('#county').val(airport.county);
        editMode = true;
        modal.hide();
        setTimeout(() => modal.show(), 150);
    }
    $('#airportModal').on('hidden.bs.modal', function(){
        // Reset modal state completely when hidden
        $('#airportForm')[0].reset();
        $('#airport_id').val('');
        editMode = false;
    });


    $('#saveAirportBtn').on('click', function(){
        const id = $('#airport_id').val();
        const data = {
            _token: '{{ csrf_token() }}',
            name: $('#name').val(),
            order_by: $('#order_by').val(),
            code: $('#code').val(),
            country: $('#country').val(),
            time_zone: $('#time_zone').val(),
            city_code: $('#city_code').val(),
            is_local: $('#is_local').val() === '1' ? 1 : 0,
            city: $('#city').val(),
            state: $('#state').val(),
            county: $('#county').val()
        };

        const url = editMode
            ? '{{ route("admin.airports.update", ":id") }}'.replace(':id', id)
            : '{{ route("admin.airports.store") }}';
        const method = editMode ? 'PUT' : 'POST';

        $.ajax({ url, type: method, data })
            .done(() => { modal.hide(); loadAirports(); _alert('Airport saved successfully.'); })
            .fail(err => _alert('Error: ' + err.responseJSON.message, 'error'));
    });

    // ------------------------ TassPro Hotel Management ------------------------
    const fetchModal = new bootstrap.Modal(document.getElementById('fetchHotelModal'));
    const editHotelModal = new bootstrap.Modal(document.getElementById('editHotelModal'));
    let previewData = [];

    $('#fetchCountryInfoBtn').on('click', function() {
        $('#fetch_country_code').val('');
        $('#previewContainer').hide();
        $('#confirmFetchBtn').hide();
        fetchModal.show();
    });

    $('#btnPreviewInfo').on('click', function() {
        const countryCode = $('#fetch_country_code').val();
        if (!countryCode) {
            _alert('Please enter a country code', 'warning');
            return;
        }

        $(this).prop('disabled', true).text('Loading...');

        $.post('{{ route("admin.country-hotels.preview") }}', {
            _token: '{{ csrf_token() }}',
            country_code: countryCode
        }, function(response) {
            $('#btnPreviewInfo').prop('disabled', false).text('Fetch Preview');
            if (response.success) {
                previewData = response.data;
                $('#previewCount').text(previewData.length);
                let html = '';
                previewData.forEach(item => {
                    html += `<tr><td>${item.key}</td><td>${item.value}</td></tr>`;
                });
                $('#previewBody').html(html);
                $('#previewContainer').show();
                $('#confirmFetchBtn').show();
            } else {
                _alert(response.message, 'error');
            }
        }).fail(() => {
            $('#btnPreviewInfo').prop('disabled', false).text('Fetch Preview');
            _alert('Error fetching data', 'error');
        });
    });

    $('#confirmFetchBtn').on('click', function() {
        const countryCode = $('#fetch_country_code').val();
        $(this).prop('disabled', true).text('Saving...');

        $.ajax({
            url: '{{ route("admin.country-hotels.store-bulk") }}',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token: '{{ csrf_token() }}',
                country_code: countryCode,
                hotels: previewData
            }),
            success: function(response) {
                $('#confirmFetchBtn').prop('disabled', false).text('Confirm & Insert');
                if (response.success) {
                    fetchModal.hide();
                    _alert(response.message);
                    location.reload();
                } else {
                    _alert(response.message, 'error');
                }
            },
            error: function() {
                $('#confirmFetchBtn').prop('disabled', false).text('Confirm & Insert');
                _alert('Error saving data', 'error');
            }
        });
    });

    $(document).on('click', '.editHotelBtn', function() {
        const id = $(this).data('id');
        $.get('{{ route("admin.country-hotels.edit", ":id") }}'.replace(':id', id), function(hotel) {
            $('#edit_hotel_id').val(hotel.id);
            $('#edit_city').val(hotel.city);
            $('#edit_destinationcode').val(hotel.destinationcode);
            $('#edit_country').val(hotel.country);
            $('#edit_nationality').val(hotel.nationality);
            $('#edit_status').val(hotel.status == true ? 1 : 0);
            editHotelModal.show();
        });
    });

    $('#hotelSearch').select2({
        theme: 'classic',
        placeholder: $('#hotelSearch').data('placeholder'),
        ajax: {
            url: '{{ route("admin.country-hotels.search") }}',
            type: 'POST',
            dataType: 'json',
            delay: 300,
            data: params => ({ _token: '{{ csrf_token() }}', term: params.term }),
            processResults: data => ({ results: data.results }),
        }
    }).on('select2:select', function (e) {
        const id = e.params.data.id;
        $.get('{{ route("admin.country-hotels.edit", ":id") }}'.replace(':id', id), function(hotel) {
            if (hotel && hotel.id) {
                $('#edit_hotel_id').val(hotel.id);
                $('#edit_city').val(hotel.city);
                $('#edit_destinationcode').val(hotel.destinationcode);
                $('#edit_country').val(hotel.country);
                $('#edit_nationality').val(hotel.nationality);
                $('#edit_status').val(hotel.status == true ? 1 : 0);
                editHotelModal.show();
                $('#hotelSearch').val(null).trigger('change');
            } else {
                _alert('Hotel not found', 'error');
            }
        }).fail(() => {
            _alert('Error fetching hotel details.', 'error');
        });
    });

    $('#updateHotelBtn').on('click', function() {
        const id = $('#edit_hotel_id').val();
        const data = {
            _token: '{{ csrf_token() }}',
            city: $('#edit_city').val(),
            destinationcode: $('#edit_destinationcode').val(),
            country: $('#edit_country').val(),
            nationality: $('#edit_nationality').val(),
            status: $('#edit_status').val()
        };

        $.post('{{ route("admin.country-hotels.update", ":id") }}'.replace(':id', id), data, function(response) {
            if (response.success) {
                editHotelModal.hide();
                _alert('Hotel updated successfully');
                location.reload();
            }
        });
    });

    $(document).on('click', '.deleteHotelBtn', function() {
        if (!confirm('Delete this record?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '{{ route("admin.country-hotels.destroy", ":id") }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                _alert('Record deleted successfully');
                location.reload();
            }
        });
    });
});
</script>
@endsection
