@extends('agent/layouts/master')

@section('title', 'Order Manage')
@section('style')
  <style>
    .box {
      border: 1px solid #dee2e6;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 1rem;
      overflow-x: auto; 
    }
    .section-title {
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }
    /* .btn-group .btn {
      margin-right: 5px;
    } */
  </style>
@endsection
@section('content')

<div class="row">
    <!-- Left Side -->
    <div class="col-md-3 left-side">
        <div class="card box">
            <label class="form-label">Select Agent</label>
            <select class="form-select mb-2">
                <option selected>sashir@ssatech.pk</option>
            </select>
        </div>

        <div class="card box">
            <div class="section-title">Ticket And Receipt Email</div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="paymentReceipt">
                <label class="form-check-label" for="paymentReceipt">Payment Receipt</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="eTicket">
                <label class="form-check-label" for="eTicket">E-ticket</label>
            </div>
            <button class="btn btn-sm btn_secondary">Send Email</button>
        </div>

        <div class="card box">
            <div class="section-title">Contact Details</div>
            <div class="mb-2">
                <input type="text" class="form-control mb-1" placeholder="Name" value="Ahmed">
                <input type="email" class="form-control mb-1" placeholder="Email" value="theremo@hotmail.com">
                <input type="text" class="form-control mb-1" placeholder="Phone" value="03238438298">
                <button class="btn btn-sm btn_primary">Update</button>
            </div>
        </div>

        <div class="box notes-box">
            <div class="section-title">Notes</div>
            <textarea id="note-editor" class="form-control"></textarea>
            <div class="d-flex justify-content-between mt-2">
                <button class="btn btn-sm btn_secondary">Add Notes</button>
                <button class="btn btn-sm btn_secondary_outline">History</button>
            </div>
        </div>

        <div class="box notes-box">
            <div class="section-title">Add Discount</div>
            <input type="text" class="form-control mb-1" placeholder="Voucher Promocode">
            <button class="btn btn-sm btn_secondary mt-2">Add Discount</button>
        </div>

        <div class="card box">
            <div class="section-title">Cancelation Charges</div>
            <div class="d-flex justify-content-start gap-3 mt-2">
                <button class="btn btn-sm btn_secondary">Cancel Order</button>
                <button class="btn btn-sm btn_secondary_outline">Refund Form</button>
            </div>
        </div>
    </div>

    <!-- Right Side -->
    <div class="col-md-9 right-side">
        <div class="d-block d-md-flex justify-content-between">
            <a href="{{ route('admin.orders') }}" class="btn btn_secondary_outline d-flex align-items-center mb-3"><i class='bx bx-chevron-left'></i> Back to Order Management</a>
            <div class="btn-group d-block">
                <button class="btn btn_secondary_outline">Show Fare Rules</button>
                <button class="btn btn_danger_outline">Guest User</button>
                <button class="btn btn_success">Ticket Issued</button>
                <button class="btn btn-outline-dark">Add Custom Product</button>
            </div>
        </div>

        <div class="card box">
            <div class="row">
                <div class="col-md-4 py-2"><strong>Order Ref:</strong> 184726</div>
                <div class="col-md-4 py-2"><strong>Web Ref:</strong> 186090634262070112063</div>
                <div class="col-md-4 py-2"><strong>Order Status:</strong> <span class="badge bg-success">TICKETISSUED</span></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4 py-2"><strong>Order Total (PKR):</strong> Rs. 200,005.00</div>
                <div class="col-md-4 py-2"><strong>Source/Affiliate:</strong> null.Affiliate Ssata App</div>
                <div class="col-md-4 py-2"><strong>IP of the User:</strong></div>
            </div>
        </div>

        <div class="card box">
        <div class="section-title">Order Details</div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Airline Ref</th>
                <th>RBD Code</th>
                <th>Cancellation Fee</th>
                <th>Flight No</th>
                <th>Origin/Destination</th>
                <th>Stops</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Class</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>ER-502</td>
                <td>NA</td>
                <td>LB</td>
                <td>ER-503</td>
                <td>KHI - ISB</td>
                <td>0</td>
                <td>22 Jul 2023 20:50</td>
                <td>22 Jul 2023 23:00</td>
                <td>Economy</td>
            </tr>
            <tr>
                <td>ER-503</td>
                <td>NA</td>
                <td>LB</td>
                <td>ER-503</td>
                <td>ISB - KHI</td>
                <td>0</td>
                <td>29 Jul 2023 15:00</td>
                <td>29 Jul 2023 17:30</td>
                <td>Economy</td>
            </tr>
            </tbody>
        </table>
        </div>

        <div class="card box">
        <div class="section-title">Ticket Details</div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Passenger Name</th>
                <th>Date Of Birth</th>
                <th>Type</th>
                <th>Nationality</th>
                <th>Passport Number / NIC</th>
                <th>Passport Expiry</th>
                <th>Airline PNR</th>
                <th>GDS PNR</th>
                <th>Ticket Number</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>MR. Nafees Ahmed</td>
                <td>1/1/1965</td>
                <td>Adult</td>
                <td>Pakistan</td>
                <td></td>
                <td></td>
                <td>CVOXMS</td>
                <td>CVOXMS-1</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>MRS. Shazia Begum</td>
                <td>1/1/1971</td>
                <td>Adult</td>
                <td>Pakistan</td>
                <td></td>
                <td></td>
                <td>CVOXMS</td>
                <td>CVOXMS-2</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>MR. Yameen Ahmed</td>
                <td>1/1/1991</td>
                <td>Adult</td>
                <td>Pakistan</td>
                <td></td>
                <td></td>
                <td>CVOXMS</td>
                <td>CVOXMS-3</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>MS. Samiya</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
        </div>

        <div class="card box">
            <div class="section-title">Product Overview</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Provider</th>
                        <th>Supplier</th>
                        <th>Base Fare</th>
                        <th>Tax</th>
                        <th>POS Rule</th>
                        <th>Displayed Price</th>
                        <th>PNR Expires At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Return Flight</td>
                        <td>EmiratesApi</td>
                        <td>Emirate</td>
                        <td>RS: 170,244</td>
                        <td>RS: 240,588</td>
                        <td>Affiliation POS Rule</td>
                        <td>RS: 197,554</td>
                        <td>26 Jul 2025 02:51 pm</td>
                        <td>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control m-0" placeholder="Update Booking Reference" aria-describedby="bookingRefBtn" aria-label="Booking Reference">
                                <button class="btn btn_secondary_outline" type="button" id="bookingRefBtn">Update</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card box">
            <div class="section-title">Payments</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Created at</th>
                        <th>Payment Method</th>
                        <th>Transaction Id</th>
                        <th>Displayed Price</th>
                        <th>Merchant Fee</th>
                        <th>Service Fee</th>
                        <th>Status</th>
                        <th>Refund Status</th>
                        <th>Selling Fare</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>26 Jul 2025 02:51 pm</td>
                            <td>Credit Card HBL - CyberSource</td>
                            <td>033-132135-465464</td>
                            <td>RS: 170,244</td>
                            <td>1.69%</td>
                            <td></td>
                            <td>Success</td>
                            <td></td>
                            <td>RS: 197,554</td>
                            <td>
                                <button class="btn btn_secondary_outline" type="button">Adjust</button>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="card box">
            <div class="section-title">Add Payment</div>
            <div class="row">
                <div class="col-3">
                    <input type="text" class="form-control" placeholder="payment amount">
                </div>
                <div class="col-3">
                    <select class="form-select">
                        <option selected>Payment Method</option>
                        <option value="creditcard">Credit Card</option>
                        <option value="banktransfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control" placeholder="payment description">
                </div>
                <div class="col-3">
                    <button class="btn btn_secondary_outline m-1" type="button">Add Payment</button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@section('script')

<!-- Summernote CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#note-editor').summernote({
            height: 150,
            placeholder: 'Write your note here...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    });
</script>

@endsection