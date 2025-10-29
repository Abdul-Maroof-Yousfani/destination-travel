@extends('admin.layouts.master')

@section('title', 'Manage Orders')

@section('content')
<div class="d-flex flex-column justify-content-between h-100">
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-4 fw-bold">Manage Orders</h2>
            {{-- <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Download Logs
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.download.logs.all', ['type' => 'emirates']) }}">All (Emirates)</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.download.logs.bookings', ['type' => 'emirates']) }}">Only Bookings (Emirates)</a></li>
                    <hr>
                    <li><a class="dropdown-item" href="{{ route('admin.download.logs.all', ['type' => 'flyjinnah']) }}">All (Fly Jinnah)</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.download.logs.bookings', ['type' => 'flyjinnah']) }}">Only Bookings (Fly Jinnah)</a></li>
                    <hr>
                    <li><a class="dropdown-item" href="{{ route('admin.download.logs.all', ['type' => 'pia']) }}">All (PIA)</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.download.logs.bookings', ['type' => 'pia']) }}">Only Bookings (PIA)</a></li>
                </ul>
            </div> --}}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-active">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Reference</th>
                        <th>Product</th>
                        <th>PNR</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Agent</th>
                        <th>Customer Details</th>
                        <th>Summary</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Charged Card</th>
                        <th>Date/Time</th>
                        <th>Action</th>
                    </tr>
                    <tr class="bg-light filters">
                        <th><input type="number" class="form-control form-control-sm" id="filterOrderId" placeholder="Order ID"></th>
                        <th><input type="text" class="form-control form-control-sm" id="filterOrderRef" placeholder="Order Reference"></th>
                        <th>
                            <select class="form-select form-select-sm" id="filterProduct">
                                <option value="">Select Product</option>
                                <option value="flight">Flight</option>
                                <option value="bus">Bus</option>
                            </select>
                        </th>
                        <th><input type="text" class="form-control form-control-sm" id="filterPNR" placeholder="PNR"></th>
                        <th>
                            <select class="form-select form-select-sm" id="filterStatus">
                                <option value="">Select Status</option>
                                <option value="initial">Initiated</option>
                                <option value="issued">Ticket Issued</option>
                                <option value="pending">Pending</option>
                                <option value="expired">Expired</option>
                                <option value="error">Error</option>
                                <option value="changed">Changed</option>
                                <option value="cancel">Cancel</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm" id="filterType">
                                <option value="">Show All</option>
                                <option value="oneway">Oneway</option>
                                <option value="return">Return</option>
                            </select>
                        </th>
                        <th><input type="text" class="form-control form-control-sm" id="filterAgent" placeholder="Agent name"></th>
                        <th colspan="7"></th>
                    </tr>
                </thead>
                <tbody id="bookingResults">
                    <tr>
                        <td colspan="13">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
            <div>
                <select id="entries" class="form-select form-select-sm d-inline-block mx-2" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        const canViewBooking = @json(auth()->user()->can('view bookings'));
        let currentPage = 1;
        const orderDetailsUrl = '{{ route("admin.orders.details", ":booking") }}';
        function fetchBookings(page = 1) {
            const data = {
                order_id: $('#filterOrderId').val(),
                order_ref: $('#filterOrderRef').val(),
                product: $('#filterProduct').val(),
                pnr: $('#filterPNR').val(),
                status: $('#filterStatus').val(),
                type: $('#filterType').val(),
                agent: $('#filterAgent').val(),
                page: page,
                per_page: $('#entries').val()
            };

            $('#bookingResults').html('<tr><td colspan="13" class="text-center">Loading...</td></tr>');

            $.ajax({
                url: '{{ route("admin.orders.fetch") }}',
                method: 'GET',
                data: data,
                success: function (response) {
                    let html = '';
                    if (response.data.length === 0) {
                        html = '<tr><td colspan="13" class="text-center">No bookings found.</td></tr>';
                    } else {
                        response.data.forEach(function (booking) {
                            let url = orderDetailsUrl.replace(':booking', booking.id);
                            html += `<tr>
                                <td><a href="${url}" class="text-decoration-underline">${booking.id}</a></td>
                                <td>${booking.order_id}</td>
                                <td>${booking.product}</td>
                                <td>${booking.flight_booking_id ?? booking.order_id ?? ''}</td>
                                <td><span class="badge bg_${booking.status === 'issued' ? 'success' : 'danger'}">${booking.status.toUpperCase()}</span></td>
                                <td>${booking.is_oneway ? 'ONEWAY' : 'RETURN'}</td>
                                <td>${booking.agent_name ?? 'Unassigned'}</td>
                                <td>
                                    ${booking.client_name ?? '-'}<br>
                                    <small>${booking.client_phone ?? '-'}</small><br>
                                    <small>${booking.client_email ?? '-'}</small>
                                </td>
                                <td>${booking.summary.replace(/\n/g, '<br>')}</td>
                                <td>0</td>
                                <td>${booking.total_tax_price}</td>
                                <td>0</td>
                                <td>${booking.created_at}</td>
                                <td>${canViewBooking ? `<a href="${url}" class="btn btn-sm btn_primary">View/Manage</a>` : ''}</td>
                            </tr>`;
                        });
                    }

                    $('#bookingResults').html(html);
                    renderPagination(response);
                },
                error: function () {
                    $('#bookingResults').html('<tr><td colspan="13">Something went wrong.</td></tr>');
                }
            });
        }

        function renderPagination(response) {
            let pages = '';
            const current = response.current_page;
            const last = response.last_page;

            if (last <= 1) {
                $('.pagination').html('');
                return;
            }

            pages += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${current - 1}">Previous</a>
                    </li>`;

            for (let i = 1; i <= last; i++) {
                pages += `<li class="page-item ${current === i ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
            }

            pages += `<li class="page-item ${current === last ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${current + 1}">Next</a>
                    </li>`;

            $('.pagination').html(pages);
        }

        $(document).on('click', '.pagination .page-link', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchBookings(page);
            }
        });

        $('.filters input, .filters select, #entries').on('input change', function () {
            currentPage = 1;
            fetchBookings(1);
        });

        $('#refreshBtn').on('click', function () {
            fetchBookings(currentPage);
        });

        fetchBookings(); // initial load
    });

</script>
@endsection
