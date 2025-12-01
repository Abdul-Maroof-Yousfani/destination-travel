
@extends('home/layouts/master')
@section('title', 'Flights')
  <title>Traveler Profile — Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@section('style')
<style>
    :root{--accent:#0ea5a4;/* teal */
    --accent-2:#0b74d1;/* blue */
    --muted:#6c757d;--card-bg:#ffffff;--page-bg:#f4f7fa;}
    body{background:var(--page-bg);font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}
    .profile-header{background:linear-gradient(90deg,rgba(14,165,164,0.06),rgba(11,116,209,0.03));border-radius:10px;padding:22px}
    .avatar{width:86px;height:86px;border-radius:16px;display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#e9f7f6,#fff);box-shadow:0 4px 14px rgba(11,116,209,0.06);}
    .stat-card{border-radius:10px;box-shadow:0 6px 18px rgba(15,20,30,0.04);background:var(--card-bg);}
    .btn-teal{background:var(--accent);color:#fff;border-radius:10px;}
    .btn-teal:hover{background:#089091}
    .tag{font-size:12px;padding:6px 8px;border-radius:6px;}
    .table thead th{font-size:13px;color:var(--muted)}
    .small-muted{color:var(--muted);font-size:13px}
    .card-section{border-radius:8px;box-shadow:0 6px 18px rgba(15,20,30,0.03);}
    /* responsive tweaks */
    @media (max-width:767px){.avatar{width:72px;height:72px}
    }
</style>
@endsection
@section('content')
  <div class="container py-4">
    <!-- header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0" style="color:#0b74d1">DestinationsTravelTour</h3>
      <div class="back-edit-buttons">
        <button class="btn btn-outline-secondary me-2"><i class="fa fa-arrow-left"></i> Back</button>
        <button class="btn btn-teal"><i class="fa fa-edit me-2"></i> Edit Profile</button>
      </div>
    </div>

    <!-- Cover Image + Profile card -->
    <div class="mb-3 position-relative">
      <img src="https://images.unsplash.com/photo-1502920514313-52581002a659?q=80&w=1200" class="w-100 rounded" style="height:180px; object-fit:cover; border-radius:12px;" />
      <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="rounded-circle border border-3 border-white position-absolute" style="width:110px; height:110px; bottom:-20px; left:20px; object-fit:cover; background:#fff;" />
    </div>

    <!-- Profile card -->
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="p-3 profile-header card-section">
          <div class="d-flex gap-3 align-items-center">
            <div class="avatar">
              <i class="fa fa-user-circle fa-3x text-muted"></i>
            </div>
            <div class="flex-grow-1">
              <h4 class="mb-1">Annie January <span class="badge bg-light text-dark ms-2">Member</span></h4>
              <div class="small-muted">annie@example.com &nbsp; • &nbsp; +92 321 1122003</div>
              <div class="mt-2">
                <span class="tag bg-light text-success me-2"><i class="fa fa-plane me-1"></i> Frequent Flyer</span>
                <span class="tag bg-light text-info"><i class="fa fa-map-marker-alt me-1"></i> Karachi, PK</span>
              </div>
            </div>
            <div class="text-end">
              <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
            </div>
          </div>

          <!-- optional quick info row -->
          <div class="row mt-3">
            <div class="col-md-4 small-muted">IP Address<br><strong class="text-dark">72.235.62.216</strong></div>
            <div class="col-md-4 small-muted">Login Provider<br><strong class="text-dark">local</strong></div>
            <div class="col-md-4 small-muted">Joined<br><strong class="text-dark">Nov 12, 2023</strong></div>
          </div>
        </div>

        <!-- Recent bookings -->
        <div class="mt-3 card-section p-3">
          <h5 class="mb-3">Recent Bookings</h5>
          <div class="table-responsive profile-destinations">
            <table class="table table-sm align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Order ID</th>
                  <th>Airline</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Price</th>
                  <th>Created</th>
                  <th>Agent</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td><strong>452GDF</strong></td>
                  <td>FlyJinnah</td>
                  <td>One-way</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td>PKR 25,431.00</td>
                  <td>Nov 12, 2023</td>
                  <td>Unassigned</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td><strong>87RTYU</strong></td>
                  <td>AirBlue</td>
                  <td>Round-trip</td>
                  <td><span class="badge bg-success">Confirmed</span></td>
                  <td>PKR 48,210.00</td>
                  <td>Oct 05, 2023</td>
                  <td>Agent123</td>
                </tr>
                <!-- more rows... -->
              </tbody>
            </table>
          </div>
        </div>
        <!-- Passengers list -->
        <div class="mt-3 card-section p-3">
          <h5 class="mb-3">Passengers</h5>
          <div class="table-responsive profile-destinations">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Nationality</th>
                  <th>Passport / CNIC</th>
                  <th>Expiry</th>
                  <th>DOB</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Annie January</td>
                  <td>ADT</td>
                  <td>PK</td>
                  <td>AB1231234</td>
                  <td>Mar 12, 2028</td>
                  <td>Feb 02, 1990</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>John Doe</td>
                  <td>ADT</td>
                  <td>US</td>
                  <td>XY9876543</td>
                  <td>May 03, 2026</td>
                  <td>Jul 18, 1985</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Right column: stats and actions -->
      <div class="col-lg-4">
        <div class="d-grid gap-3">
          <div class="p-3 stat-card text-center">
            <div class="small-muted">Total Bookings</div>
            <h3 class="my-1">1</h3>
            <div class="small-muted">This year</div>
          </div>

          <div class="p-3 stat-card text-center">
            <div class="small-muted">Total Passengers</div>
            <h3 class="my-1">1</h3>
            <div class="small-muted">Active profiles</div>
          </div>

          <div class="p-3 stat-card text-center">
            <div class="small-muted">Total Revenue</div>
            <h3 class="my-1 text-success">PKR 0.00</h3>
            <div class="small-muted">Recorded</div>
          </div>

          <div class="p-3 card-section">
            <h6>Actions</h6>
            <div class="d-grid gap-2 mt-2">
              <button class="btn btn-outline-primary"><i class="fa fa-file-invoice me-2"></i> Export Invoices</button>
              <button class="btn btn-outline-secondary"><i class="fa fa-user-plus me-2"></i> Add Passenger</button>
              <button class="btn btn-outline-danger"><i class="fa fa-ban me-2"></i> Block User</button>
            </div>
          </div>

          <div class="p-3 card-section small-muted">
            <h6 class="mb-2">Notes</h6>
            <p class="mb-0" style="font-size:13px">Customer prefers window seat. VIP membership expiring in 30 days. Passport needs renewal reminder.</p>
          </div>
        </div>
      </div>
    </div>
    <footer class="mt-4 text-center small-muted">&copy; <strong>DestinationsTravelTour</strong> — Designed for smooth airline booking management.</footer>
  </div>
    @section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endsection
@endsection