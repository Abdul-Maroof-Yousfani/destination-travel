@php
    // Robust extraction for rooms from pre-book breakup
    $rooms = $breakup['hotel']['rooms']['room'] ?? 
             $breakup['hotel']['rooms'] ?? 
             $breakup['rooms']['room'] ?? [];
             
    // Handle single room object vs array
    if (isset($rooms['roomName']) || (isset($rooms['roomIdentifier']) && !isset($rooms[0]))) {
        $rooms = [$rooms];
    }

    $currency = $request['currency'] ?? 'AED';
    // Pricing fallbacks
    $rooms = $breakup['hotel']['rooms']['room'] ?? [];

    if (isset($rooms['roomName'])) {
        $rooms = [$rooms];
    }

    $totalNet = 0;
    $totalGross = 0;
    $totalTax = 0;

    foreach ($rooms as $room) {
        $totalNet += $room['price']['supplierNet'] ?? 0;
        $totalGross += $room['price']['supplierGross'] ?? 0;
        $totalTax += $room['price']['supplierTax'] ?? 0;

        $currency = $room['price']['supplierCurrency'] ?? $currency;
    }
    // dd($totalNet, $totalGross, $totalTax);
@endphp

<style>
.form-control
 {
    display: block;
    width: 100%;
    height: calc(1.5em + .75rem + 2px);
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
.form-group label {
    font-size: 14px;
    margin-bottom: 10px;
}
. {
    border: 1px solid #d9d9d9;
    border-radius: 4px;
    padding: 10px 55px 5px 5px;
    width: 100%;
}
    .col-md-4 {
        -ms-flex: 0 0 33.333333%;
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
        margin-bottom: 20px;
    }
label.form-label.small.fw-bold {
    margin-bottom: 6px;
    margin-top: 15px;
}   

    .btn-primary {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:active {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:focus {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled:hover {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled:active {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled:focus {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .text-primary {
        color: #00788a !important;
    }

    h4.heading-hotl {
        color: #000 !important;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    h6.parag-hotl {
        margin-bottom: 5px !important;
    }

    button.comb-hotl {
        font-size: 14px;
    }
        .bg-primary {
        background-color: #00788a !important;
    }

   .badge {
    color: #fff;
    margin-right: 7px;
}
.adul span {
    margin-bottom: 5px;
    font-weight: 800;
}
</style>

<div class="booking-step-content" data-step="2">
    <div class="row">
        <div class="col-lg-12">
            <div class="checkout-form-card p-4 bg-white rounded-4 shadow-sm">
                <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-user-pen me-2"></i> Guest Details</h4>
                
                <form id="hotel-checkout-ajax-form">
                    @csrf
                    <!-- Hidden fields required for booking -->
                    <input type="hidden" name="hotel_id" value="{{ $hotel['code'] }}">
                    <input type="hidden" name="session_id" value="{{ $request['session_id'] }}">
                    <input type="hidden" name="hotel_name" value="{{ $hotel['name'] }}">
                    <input type="hidden" name="hotel_city" value="{{ $hotel['city'] }}">
                    <input type="hidden" name="check_in" value="{{ $request['check_in'] ?? '' }}">
                    <input type="hidden" name="check_out" value="{{ $request['check_out'] ?? '' }}">
                    <input type="hidden" name="group_code" value="{{ $request['group_code'] }}">
                    <input type="hidden" name="destination_code" value="{{ $request['destination_code'] ?? 'AE' }}">
                    {{-- <input type="hidden" name="nationality" value="{{ $request['nationality'] ?? 'AE' }}"> --}}
                    <input type="hidden" name="currency" value="{{ $currency }}">
                    <input type="hidden" name="total_net" value="{{ $totalNet }}">
                    <input type="hidden" name="total_gross" value="{{ $totalGross }}">
                    <input type="hidden" name="total_tax" value="{{ $totalTax }}">
                    <input type="hidden" name="raw_prebook_response" value="{{ json_encode($breakup) }}">

                    <!-- Contact Information -->
                    <div class="contact-section mb-5 p-3 " style="background: #f8fbff; border: 1px solid #e1e8f0;">
                        <h6 class="fw-bold mb-3 text-secondary">Contact Information</h6>
                        <div class="row g-3">
                            <div class="col-md-12 col-lg-4">
                                <label class="form-label small fw-bold ">Email Address</label>
                                <input type="email" name="email" id="contact-email" class="form-control  verify-client-input" required placeholder="your@email.com" value="{{ $client->email ?? '' }}" {{ $client ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-12 col-lg-4">
                                <label class="form-label small fw-bold ">Phone Number</label>
                                <input type="text" name="phone" id="contact-phone" class="form-control  verify-client-input" required placeholder="+971 XXX XXXX" value="{{ $client->phone ?? '' }}" {{ $client ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-12 col-lg-4">
                                <label class="form-label small fw-bold ">Lead Guest First Name</label>
                                <input type="text" name="first_name" class="form-control " required placeholder="First Name">
                            </div>
                            <div class="col-md-12 col-lg-4">
                                <label class="form-label small fw-bold ">Lead Guest Last Name</label>
                                <input type="text" name="last_name" class="form-control " required placeholder="Last Name">
                            </div>
                            <div class="col-md-12 col-lg-4">
                                <label class="form-label small fw-bold ">Nationality</label>
                                <select name="nationality" class="form-control " required>
                                    <option value="">Select Nationality</option>
                                    @foreach(config('variables.nationalities') as $key => $value)
                                        <option value="{{ $key }}">{{ $value['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 col-lg-4"></div>
                            <div class="col-md-12 col-lg-4"></div>
                        </div>
                    </div>

                    @forelse($rooms as $index => $room)
                        @php
                            $roomIdentifier = $room['roomIdentifier'] ?? ($index + 1);
                            $adults = (int)($room['adult'] ?? $room['Adult'] ?? 1);
                            $children = (int)($room['children']['count'] ?? $room['children'] ?? 0);
                            $rateKeys = (array)($room['rateKeys'] ?? $room['rateKey'] ?? []);
                            
                            $roomDataForInput = [
                                'roomIdentifier' => $roomIdentifier,
                                'roomName' => $room['roomName'] ?? 'Standard Room',
                                'meal' => $room['meal'] ?? 'N/A',
                                'rateKey' => $rateKeys[0] ?? '',
                                'rateType' => $room['rateType'] ?? 'N/A',
                                'policies' => $room['policies'] ?? null,
                                'price' => [
                                    'supplierNet' => $room['price']['supplierNet'] ?? $room['price']['net'] ?? 0,
                                    'supplierGross' => $room['price']['supplierGross'] ?? $room['price']['gross'] ?? 0,
                                    'supplierTax' => $room['price']['supplierTax'] ?? $room['price']['tax'] ?? 0,
                                ]
                            ];
                        @endphp
                        
                        <div class="room-guest-section mb-4 p-4 border rounded-4 bg-white shadow-sm">
                            <input type="hidden" name="rooms_data[{{ $index }}]" value="{{ json_encode($roomDataForInput) }}">
                            <div class="guests-container">
                                <div class="row align-items-center mb-4">
                                    <div class="col-md-6">
                                        <h5 class="fw-bold m-0 d-flex align-items-center">
                                            <span class="badge bg-primary  me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                                            <div class="adul flex-grow-1">
                                                <span class="d-block">{{ $room['roomName'] ?? 'Room ' . ($index + 1) }}</span>
                                                <small class="text-muted fw-normal" style="font-size: 0.75rem;">
                                                    <i class="fa-solid fa-users me-1"></i> {{ $adults }} Adult(s) 
                                                    @if($children > 0) · {{ $children }} Child(ren) @endif
                                                    · {{ $room['meal'] ?? 'Room Only' }}
                                                </small>
                                            </div>
                                        </h5>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div>
                                            <div class="autofill-container text-end">
                                                <div class="d-flex align-items-center overflow-hidden">
                                                    <label  style="display: flex;gap: 7px;"class="small fw-bold text-muted me-2 border-end pe-2 "><i class="fa-solid fa-bolt text-warning me-1"></i> Autofill</label>
                                                    <select style="width:100% !important" class="form-select form-control form-select form-control-sm border-0 bg-light saved-traveler-dropdown" {{ $client ? '' : 'disabled' }} style="width: auto; min-width: 150px;">
                                                        <option value="">Select Traveler</option>
                                                        @if($client)
                                                            @foreach($savedPassengers as $sp)
                                                                <option value="{{ $sp->id }}" 
                                                                    data-title="{{ $sp->title }}"
                                                                    data-fname="{{ $sp->given_name }}"
                                                                    data-lname="{{ $sp->surname }}"
                                                                    data-dob="{{ $sp->dob ? \Carbon\Carbon::parse($sp->dob)->format('Y-m-d') : '' }}"
                                                                    data-nationality="{{ $sp->nationality }}">
                                                                    {{ $sp->given_name }} {{ $sp->surname }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                @if(!$client)
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">
                                                        <a href="{{ route('login') }}" class="text-primary fw-bold">Sign in</a> to view saved travelers
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="small fw-bold text-uppercase text-primary mb-3" style="letter-spacing: 0.5px;">Adult Guests</h6>
                                @for($a = 0; $a < $adults; $a++)
                                    <div class="guest-row mb-3 pb-3 {{ $a < ($adults - 1) || $children > 0 ? 'border-bottom' : '' }}">
                                        <div class="row g-3">
                                            <div class="col-md-2">
                                                <label class="form-label  small fw-bold">Title</label>
                                                <select name="guests[{{ $index }}][{{ $a }}][title]" class="form-select form-control form-control   bg-light border-0">
                                                    <option value="Mr">Mr.</option>
                                                    <option value="Ms">Ms.</option>
                                                    <option value="Mrs">Mrs.</option>
                                                    <option value="Miss">Miss</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label   small fw-bold">First Name</label>
                                                <input type="text" name="guests[{{ $index }}][{{ $a }}][first_name]" class="form-control  bg-light border-0" required placeholder="First Name">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label  small fw-bold">Last Name</label>
                                                <input type="text" name="guests[{{ $index }}][{{ $a }}][last_name]" class="form-control  bg-light border-0" required placeholder="Last Name">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label  small fw-bold">Date of Birth</label>
                                                <input type="date" name="guests[{{ $index }}][{{ $a }}][dob]" class="form-control  bg-light border-0" required placeholder="Date of Birth">
                                            </div>
                                            <input type="hidden" name="guests[{{ $index }}][{{ $a }}][type]" value="adult">
                                        </div>
                                    </div>
                                @endfor

                                <!-- Children -->
                                @php
                                    $childAges = $room['children']['childAge'] ?? [];
                                    if (!is_array($childAges) && $children > 0) {
                                        $childAges = array_fill(0, $children, ['text' => '8']); // Fallback age
                                    }
                                @endphp
                                
                                @if($children > 0)
                                    <h6 class="small fw-bold text-uppercase text-warning mt-4 mb-3" style="letter-spacing: 0.5px;">Child Guests</h6>
                                    @foreach($childAges as $cIndex => $child)
                                        @php
                                            $childAge = is_array($child) ? ($child['text'] ?? 8) : $child;
                                            $childAge = (int) $childAge;

                                            // Generate approximate DOB from age
                                            $dob = now()->subYears($childAge)->format('Y-m-d');
                                            $guestIndex = $adults + $cIndex;
                                        @endphp
                                        <div class="guest-row mb-3 pb-3 {{ $cIndex < (count($childAges) - 1) ? 'border-bottom' : '' }}">
                                            <div class="row g-3">
                                                <div class="col-md-2">
                                                    <label class="form-label  small fw-bold">Title</label>
                                                    <select name="guests[{{ $index }}][{{ $guestIndex }}][title]" class="form-select form-control  bg-light border-0">
                                                        <option value="Mstr">Mstr.</option>
                                                        <option value="Miss">Miss</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label  small fw-bold">First Name (Child - Age {{ is_array($child) ? ($child['text'] ?? '8') : $child }})</label>
                                                    <input type="text" name="guests[{{ $index }}][{{ $adults + $cIndex }}][first_name]" class="form-control  bg-light border-0" required placeholder="First Name">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label  small fw-bold">Last Name (Child)</label>
                                                    <input type="text" name="guests[{{ $index }}][{{ $adults + $cIndex }}][last_name]" class="form-control  bg-light border-0" required placeholder="Last Name">
                                                </div>
                                                @php
                                                    $checkInDate = \Carbon\Carbon::parse($request['check_in'] ?? now());
                                                    $maxDob = $checkInDate->copy()->subYears($childAge);
                                                    $minDob = $maxDob->copy()->subYear()->addDay();
                                                    $defaultDob = $maxDob->format('Y-m-d');
                                                @endphp
                                                <div class="col-md-5">
                                                    <label class="form-label  small fw-bold">Date of Birth</label>
                                                    <input type="date" name="guests[{{ $index }}][{{ $guestIndex }}][dob]" value="{{ $defaultDob }}" 
                                                        min="{{ $minDob->format('Y-m-d') }}"
                                                        max="{{ $maxDob->format('Y-m-d') }}"
                                                        class="form-control  bg-light border-0" required>
                                                </div>
                                                <input type="hidden" name="guests[{{ $index }}][{{ $adults + $cIndex }}][type]" value="child">
                                                <input type="hidden" name="guests[{{ $index }}][{{ $adults + $cIndex }}][age]" value="{{ is_array($child) ? ($child['text'] ?? '8') : $child }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning rounded-4 shadow-sm">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> No room details found for this selection. Please search for the hotel again.
                        </div>
                    @endforelse

                    <div class="remarks-section mb-4">
                        <label class="form-label   small fw-bold">Special Requests (Optional)</label>
                        <textarea name="remarks" class="form-control rounded-4" rows="3" placeholder="e.g. Early check-in, honeymoon, high floor..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-5 prev-step-btn">Back</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 save-booking-ajax-btn">
                            Save & Continue <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. Autofill Logic
    $('.saved-traveler-dropdown').on('change', function() {
        const $dropdown = $(this);
        const $option = $dropdown.find(':selected');
        if (!$option.val()) return;

        const $roomSection = $dropdown.closest('.room-guest-section');
        const $firstAdultFname = $roomSection.find('input[name$="[first_name]"]').first();
        const $firstAdultLname = $roomSection.find('input[name$="[last_name]"]').first();
        const $firstAdultDob = $roomSection.find('input[name$="[dob]"]').first();
        const $firstAdultTitle = $roomSection.find('select[name$="[title]"]').first();

        // Check if the current room has an empty first guest, or just target the first one found
        // For simplicity, we usually autofill the first guest in the room where the dropdown is clicked
        $firstAdultFname.val($option.data('fname'));
        $firstAdultLname.val($option.data('lname'));
        $firstAdultDob.val($option.data('dob'));
        $firstAdultTitle.val($option.data('title'));

        // Reset dropdown after selective autofill or keep it? 
        // Typically keep it for reference or reset so they can select for other guests?
        // Actually, we should probably check if it's already used.
    });

    // 2. Client Verification (New Email/Phone check)
    let verifyTimeout;
    $('.verify-client-input').on('input', function() {
        clearTimeout(verifyTimeout);
        const $input = $(this);
        
        // Don't verify if already logged in (inputs are readonly anyway)
        @if($client) return; @endif

        verifyTimeout = setTimeout(function() {
            const email = $('#contact-email').val();
            const phone = $('#contact-phone').val();

            if (email && phone && email.includes('@')) {
                $.ajax({
                    url: '{{ route("verify.client") }}',
                    method: 'POST',
                    data: { email: email, phone: phone },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response) {
                        // All good
                        $input.removeClass('is-invalid').addClass('is-valid');
                    },
                    error: function(xhr) {
                        if (xhr.status === 400) {
                            _alert(xhr.responseJSON.message || 'Email or phone already exists.', 'warning');
                            $input.addClass('is-invalid');
                        }
                    }
                });
            }
        }, 800);
    });
});
</script>
