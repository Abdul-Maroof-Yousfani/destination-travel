@extends('admin/layouts/master')

@section('title', 'Order List')
@section('style')
{{-- style --}}
@endsection
@section('content')
<div class="d-flex flex-column justify-content-between h-100">
    <div>
        <h2 class="mb-4 fw-bold">Manage Orders</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-active">
                <tr>
                <th>Order ID</th>
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
            </thead>
            <tbody>
                <tr class="bg-light">
                    <th><input type="text" class="form-control form-control-sm" placeholder="Order ID"></th>
                    <th>
                        <select class="form-select form-select-sm">
                            <option selected>Select Product</option>
                            <option value="flight">Flight</option>
                            <option value="bus">Bus</option>
                        </select>
                    </th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="PNR"></th>
                    <th>
                        <select class="form-select form-select-sm">
                            <option selected>Select Status</option>
                            <option value="initiated">Initiated</option>
                            <option value="ticketissued">Ticket Issued</option>
                            <option value="pending">Pending</option>
                        </select>
                    </th>
                    <th>
                        <select class="form-select form-select-sm">
                            <option selected>Show All</option>
                            <option value="oneway">Oneway</option>
                            <option value="return">Return</option>
                        </select>
                    </th>
                    <th>
                        <select class="form-select form-select-sm">
                            <option selected>Show All</option>
                            <option value="oneway">Oneway</option>
                            <option value="return">Return</option>
                        </select>
                    </th>
                    <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder=""></th>
                    <th></th>
                    <th>
                        <select class="form-select form-select-sm">
                            <option value="asc">New</option>
                            <option value="desc">Old</option>
                        </select>
                    </th>
                    <th></th>
                </tr>
                @for ($i = 0; $i < 5; $i++)
                    <tr>
                    <td>2638812</td>
                    <td>FLIGHT</td>
                    <td>UX0LYF</td>
                    <td><span class="badge bg_{{ $i % 2 ? 'danger' : 'success' }}">{{ $i % 2 ? 'INITIATED' : 'Ticket Issued' }}</span></td>
                    <td>ONEWAY</td>
                    <td>Unassigned</td>
                    <td>
                        Mohammad Mohsin<br>
                        <small>+923049208101</small><br>
                        <small>mohsin354@gmail.com</small>
                    </td>
                    <td>ONEWAY<br>Y PA-200<br>KH-ISB</td>
                    <td>0</td>
                    <td>17,715</td>
                    <td>0</td>
                    <td>9/22/2023 01:27</td>
                    <td><a href="{{ route('admin.order', $i) }}" class="btn btn-sm btn_primary">View/Manage</a></td>
                    </tr>
                @endfor
                <!-- More rows as needed -->
            </tbody>
            </table>
        </div>
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
@endsection
@section('script')
{{-- script --}}
@endsection