{{-- {{$data}} --}}

@extends('admin.app')
{{-- @section('title') {{ $pageTitle }} @endsection --}}
@section('content')
@php
    $package_type = isset($_GET['package']) ? $_GET['package'] : "buyer";
@endphp
<div class="inner-content">
    <div class="page-tabs-row">
        <ul class="nav nav-tabs watchlist-tabs" id="productsServicesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{$package_type=='buyer'?"active":""}}" id="buyers-tab" data-bs-toggle="tab" data-bs-target="#buyers" type="button" role="tab" aria-controls="buyers" aria-selected="true">Buyer</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{$package_type=='seller'?"active":""}}" id="sellers-tab" data-bs-toggle="tab" data-bs-target="#sellers" type="button" role="tab" aria-controls="sellers" aria-selected="false">Seller</button>
            </li>
        </ul>
    </div>
    <div class="tab-content report-table-box">
         <div class="tab-pane fade {{$package_type=='buyer'?"show active":""}}" id="buyers" role="tabpanel" aria-labelledby="buyers-tab" tabindex="0">
            <div class="heading-row">
                    @php
                        $user = App\Models\User::find($id);
                    @endphp
                    <h3><strong>{{ $user ? $user->name : 'User' }}</strong> -> Buyer Package History</h3>
                <div class="d-flex">  
                    <a href="{{route('admin.user.index')}}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="icon-park-twotone:back"></iconify-icon>
                        Back 
                    </a>            
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <table class="table">
                        <thead>
                            <tr class="align-middle">
                                <th>SL.</th>
                                <th>Package Name</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Package Credit unit</th>
                                <th>Start date</th>
                                <th>Expiry date</th>

                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            @if($buyer_currernt_package)
                            <tr>
                                <td> {{ '#' }}</td>
                                <td> {{ $buyer_currernt_package->package_data ? $buyer_currernt_package->package_data->package_name :"" }}</td>      
                                <td> {{ $buyer_currernt_package->package_duration}} Months</td>         
                                <td> {{ $buyer_currernt_package->purchase_amount }}</td>      
                                <td> {{ $buyer_currernt_package->monthly_credit }}</td>    
                                <td>{{date("d-m-Y",strtotime($buyer_currernt_package->created_at))}}</td>
                                <td>{{date("d-m-Y",strtotime($buyer_currernt_package->expiry_date))}}</td>
                            </tr>
                            @endif
                            <tr class="bg-primary text-white">
                                <td colspan="100%" class="text-center">User old Buyer Package</td>
                            </tr>
                            @forelse ($buyer_old_package as $key =>$item)
                            @php
                                $seller_Package = App\Models\SellerPackage::where('id',$item->package_Id)->first();
                            @endphp
                    
                            <tr>
                                <td> {{ $loop->index+1 }}</td>
                                <td> {{ $seller_Package->package_name}}</td>      
                                <td> {{ $item->package_duration }}</td>      
                                <td> {{ $item->package_amount }}</td>      
                                <td> {{ $item->package_credit }}</td>     
                                <td>{{date("d-m-Y",strtotime($item->purchase_date))}}</td>
                                <td>{{date("d-m-Y",strtotime($item->expiry_date))}}</td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-center">No records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>  
                <div class="col-lg-4">
                    <div>
                        @if (session('success'))
                            <div class="alert alert-success" id="message_div">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                    <div>
                        @if (session('error'))
                            <div class="alert alert-danger" id="message_div">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div>
                        @if (session('warning'))
                            <div class="alert alert-warning" id="message_div">
                                {{ session('warning') }}
                            </div>
                        @endif
                    </div>
                    <form method="post" action="{{route('admin.buy.buyer.package')}}">
                        @csrf
                        <label>Buy Buyer Package From Admin</label>
                        <br>
                        <select class="form-control" name="user_buyer_package" id="user_buyer_package">
                            <option value="" selected hidden>--select--</option>
                            @foreach($allBuyerPackages as $buyerPackage)
                                <option value="{{ $buyerPackage->id }}" 
                                data-duration="{{ $buyerPackage->package_duration }}" 
                                data-price="{{ $buyerPackage->package_price }}" 
                                data-credits="{{ $buyerPackage->total_number_of_auction }}"
                                data-package_type="{{ $buyerPackage->type }}"
                                >{{ $buyerPackage->package_name }}</option>
                            @endforeach
                        </select>
                        <br>
                        <input class="form-control" type="text" name="buyer_package_duration" id="buyer_package_duration" placeholder="Duration" readonly>
                        <br>
                        <input class="form-control" type="text" name="buyer_package_price" id="buyer_package_price" placeholder="Package Price" readonly>
                        <br>
                        <input class="form-control" type="text" name="buyer_package_credits" id="buyer_package_credits" placeholder="Credits per package" readonly>
                        <input class="form-control" type="hidden" name="buyer_package_type" id="buyer_package_type">
                        <br>
                        <textarea class="form-control" name="buyer_package_notes" id="buyer_package_notes" cols="30" rows="3" placeholder="Notes" required></textarea>
                        <input type="hidden" name="user_id" value="{{$id}}">
                        <input type="hidden" name="form_type" value="new">
                        <button class="btn btn-primary" type="submit">Buy</button>
                    </form>
                </div>
            </div>
         </div>
         <div class="tab-pane fade {{$package_type=='seller'?"show active":""}}" id="sellers" role="tabpanel" aria-labelledby="sellers-tab" tabindex="0">
            <div class="heading-row">
            <h3><strong>{{ $user ? $user->name : 'User' }}</strong> -> Seller Package History</h3>
                <div class="d-flex">  
                    <a href="{{route('admin.user.index')}}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="icon-park-twotone:back"></iconify-icon>
                        Back 
                    </a>            
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <table class="table">
                        <thead>
                            <tr class="align-middle">
                                <th>SL.</th>
                                <th>Package Name</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Monthly Credit unit</th>
                                <th>Start date</th>
                                <th>Expiry date</th>

                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            @if($seller_currernt_package)
                            <tr>
                                <td> {{ '#' }}</td>
                                <td> {{ $seller_currernt_package->getPackageDetails ? $seller_currernt_package->getPackageDetails->package_name :"" }}</td>      
                                <td> {{ $seller_currernt_package->package_duration}} Months</td>         
                                <td> {{ $seller_currernt_package->purchase_amount }}</td>      
                                <td> {{ $seller_currernt_package->monthly_credit }}</td>    
                                <td>{{date("d-m-Y",strtotime($seller_currernt_package->created_at))}}</td>
                                <td>{{date("d-m-Y",strtotime($seller_currernt_package->expiry_date))}}</td>
                            </tr>
                            @endif
                            <tr class="bg-primary text-white">
                                <td colspan="100%" class="text-center">User old Seller Package</td>
                            </tr>
                            @forelse ($seller_old_package as $key =>$item)
                            @php
                                $seller_Package = App\Models\SellerPackage::where('id',$item->package_id)->first();
                            @endphp
                            <tr>
                                <td> {{ $loop->index+1 }}</td>
                                <td> {{ $seller_Package->package_name}}</td>      
                                <td> {{ $item->package_duration }}</td>      
                                <td> {{ $item->purchase_amount }}</td>      
                                <td> {{ $item->monthly_credit }}</td>     
                                <td>{{date("d-m-Y",strtotime($item->purchase_date))}}</td>
                                <td>{{date("d-m-Y",strtotime($item->expiry_date))}}</td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-center">No records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-4">
                    <div>
                        @if (session('success'))
                            <div class="alert alert-success" id="message_div">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                    <div>
                        @if (session('error'))
                            <div class="alert alert-danger" id="message_div">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div>
                        @if (session('warning'))
                            <div class="alert alert-warning" id="message_div">
                                {{ session('warning') }}
                            </div>
                        @endif
                    </div>
                    <form method="post" action="{{route('admin.buy.seller.package')}}">
                        @csrf
                        <label>Buy Seller Package From Admin</label>
                        <br>
                        <select class="form-control" name="user_seller_package" id="user_seller_package">
                            <option value="" selected hidden>--select--</option>
                            @foreach($allSellerPackages as $sellerPackage)
                                <option value="{{ $sellerPackage->id }}" 
                                data-duration="{{ $sellerPackage->package_duration }}" 
                                data-price="{{ $sellerPackage->package_price }}" 
                                data-credits="{{ $sellerPackage->credit }}"
                                data-package_type="{{ $sellerPackage->package_type }}"
                                >{{ $sellerPackage->package_name }}</option>
                            @endforeach
                        </select>
                        <br>
                        <input class="form-control" type="text" name="seller_package_duration" id="seller_package_duration" placeholder="Duration" readonly>
                        <br>
                        <input class="form-control" type="text" name="seller_package_price" id="seller_package_price" placeholder="Package Price" readonly>
                        <br>
                        <input class="form-control" type="text" name="seller_package_credits" id="seller_package_credits" placeholder="Credits per package" readonly>
                        <input class="form-control" type="hidden" name="seller_package_type" id="seller_package_type">
                        <br>
                        <textarea class="form-control" name="seller_package_notes" id="seller_package_notes" cols="30" rows="3" placeholder="Notes" required></textarea>
                        <input type="hidden" name="user_id" value="{{$id}}">
                        <input type="hidden" name="form_type" value="new">
                        <button class="btn btn-primary" type="submit">Buy</button>
                    </form>
                </div>
            </div>
         </div>
    </div>
</div>
@endsection
@push('scripts')
{{-- <script>
    $('.itemremove').on("click", function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "banner/delete/" + id;
            } else {
                Swal.fire("Cancelled", "Record is safe", "error");
            }
        });
    });
</script> --}}


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const packageSelect = document.getElementById('user_buyer_package');
        const durationInput = document.getElementById('buyer_package_duration');
        const priceInput = document.getElementById('buyer_package_price');
        const creditsInput = document.getElementById('buyer_package_credits');
        const packageTypeInput = document.getElementById('buyer_package_type');

        packageSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const duration = selectedOption.getAttribute('data-duration');
            const price = selectedOption.getAttribute('data-price');
            const credits = selectedOption.getAttribute('data-credits');
            const package_type = selectedOption.getAttribute('data-package_type');

            durationInput.value = duration || '';
            priceInput.value = price || '';
            creditsInput.value = credits || '';
            packageTypeInput.value = package_type || '';
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const packageSelect = document.getElementById('user_seller_package');
        const durationInput = document.getElementById('seller_package_duration');
        const priceInput = document.getElementById('seller_package_price');
        const creditsInput = document.getElementById('seller_package_credits');
        const packageTypeInput = document.getElementById('seller_package_type');

        packageSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const duration = selectedOption.getAttribute('data-duration');
            const price = selectedOption.getAttribute('data-price');
            const credits = selectedOption.getAttribute('data-credits');
            const packageType = selectedOption.getAttribute('data-package_type');

            durationInput.value = duration || '';
            priceInput.value = price || '';
            creditsInput.value = credits || '';
            packageTypeInput.value = packageType || '';
        });

        // Trigger change event on page load to set initial values if any option is preselected
        if (packageSelect.selectedIndex !== -1) {
            packageSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush