@extends('front.layout.app')
@section('section')
@php
    $package_type = isset($_GET['package']) ? $_GET['package'] : "buyer";
@endphp
<div class="main">
    <div class="inner-page">

        <div class="profile-page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    @include('front.user.layout.sidebar')
                    <div class="col-xxl-9 col-xl-8 col-12 profile-right">
                        <div class="sidebar-toggler">
                            <span class="sidebar-opener" id="sidebarOpener">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M385.1 219.9 199.2 34c-20-20-52.3-20-72.3 0s-20 52.3 0 72.3L276.7 256 126.9 405.7c-20 20-20 52.3 0 72.3s52.3 20 72.3 0l185.9-185.9c19.9-19.9 19.9-52.3 0-72.2z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                            </span>
                        </div>
                        <div class="tab-panes-wrapper">
                            <div class="tab-content">
                                <div class="tab-content-wrapper">
                                    <div class="top-content-bar"></div>
                                    <div class="m-2">
                                        {{-- @if (session('success'))
                                            <div class="alert alert-success" id="message_div">
                                                {{ session('success') }}
                                            </div>
                                        @endif --}}
                                    </div>
                                    <div class="content-box">
                                        <div class="inner">
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
                                            <div class="tab-content settings-cta-box">
                                                <div class="tab-pane fade {{$package_type=='buyer'?"show active":""}}" id="buyers" role="tabpanel" aria-labelledby="buyers-tab" tabindex="0">
                                                    <h5>Buyer Wallet Section</h5>
                                                    <div class="cta-row">
                                                        <a href="#">
                                                            <span>Current Package</span>
                                                            @if($my_current_buyer_package)
                                                                @if($my_current_buyer_package->package_data)
                                                                    <h5>{{$my_current_buyer_package->package_data->package_name}}</h5> 
                                                                @endif
                                                            @else
                                                                <h5>No Package Availabe Yet!</h5>   
                                                            @endif
                                                        </a>       
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="#">
                                                            <span>Expiry Date</span>
                                                            @if($my_current_buyer_package)
                                                            <h5>{{ date('d M, Y H:i:s A', strtotime($my_current_buyer_package->expiry_date)) }}</h5>
                                                                @else
                                                                <h5>###</h5>   
                                                            @endif
                                                        </a>       
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="#">
                                                            <span>Available Balance</span>
                                                            @if($buyer_walletBalance)  
                                                            <h5>{{$buyer_walletBalance->current_unit}} Credits</h5> 
                                                            @else
                                                            <h5>0.00 Credits</h5>   
                                                            @endif
                                                        </a>
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="{{ route('user.buyer_wallet_transaction')}}">
                                                            <span>Wallet History</span>
                                                            <img src="{{asset('frontend/assets/images/angle-right.png')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="{{ route('user.buyer_package_history')}}">
                                                            <span>Package History</span>
                                                            <img src="{{asset('frontend/assets/images/angle-right.png')}}" alt="">
                                                        </a>
                                                    </div>
                                                    @if($buyer_walletBalance)
                                                        <div class="content-box bg-gray-1">
                                                            <div class="inner">
                                                                <h3 class="mb-4">Credit Usages As a Buyer</h3>
                                                                <div class="credit-charts">
                                                                    <canvas id="buyer-creditChart" style="width:632px;height:632px;"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="tab-pane fade {{$package_type=='seller'?"show active":""}}" id="sellers" role="tabpanel" aria-labelledby="sellers-tab" tabindex="0">
                                                    <h5>Seller Wallet Section</h5>
                                                    <div class="cta-row">
                                                        <a href="#">
                                                            <span>Current Package</span>
                                                            @if($my_current_seller_package)
                                                                @if($my_current_seller_package->getPackageDetails)
                                                                    <h5>{{$my_current_seller_package->getPackageDetails->package_name}}</h5> 
                                                                @endif
                                                            @else
                                                                <h5>No Package Availabe Yet!</h5>   
                                                            @endif
                                                        </a>       
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="#">
                                                            <span>Expiry Date</span>
                                                            @if($my_current_seller_package)
                                                            <h5>{{ date('d M, Y H:i:s A', strtotime($my_current_seller_package->expiry_date)) }}</h5>
                                                                @else
                                                                <h5>###</h5>   
                                                            @endif
                                                        </a>       
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="#">
                                                            <span>Available Credit</span>
                                                            @if($seller_walletBalance)
                                                                
                                                            <h5>{{$seller_walletBalance->current_unit}}</h5>
                                                                
                                                            @else
                                                            <h5>0.00</h5>   
                                                            @endif
                                                        </a>
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="{{ route('user.seller_wallet_transaction')}}">
                                                            <span>Wallet History</span>
                                                            <img src="{{asset('frontend/assets/images/angle-right.png')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="cta-row">
                                                        <a href="{{ route('user.seller_package_history')}}">
                                                            <span>Package History</span>
                                                            <img src="{{asset('frontend/assets/images/angle-right.png')}}" alt="">
                                                        </a>
                                                    </div>
                                                    @if($seller_walletBalance)
                                                        <div class="content-box bg-gray-1">
                                                            <div class="inner">
                                                                <h3>Credit Usages As a Seller</h3>
                                                                <div class="credit-charts">
                                                                    <canvas id="seller-creditChart"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        const consumptionChart = new Chart(document.getElementById("buyer-creditChart"), {
        type: "pie",
            data: {
                labels: [
                    'Credits Left',
                    'Credits Used'
                ],
                datasets: [{
                    label: '',
                    data: ["{{$buyer_credit_left}}", "{{$buyer_credit_used}}"],
                    backgroundColor: [
                        '#30BA00',
                        '#D82C42'
                    ],
                    hoverOffset: 0,
                    borderWidth: 0,
                    maxHeight: 16,
                    maxHeight: 16
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 16,
                            boxHeight: 16,
                            color: '#000000',
                            padding: 20,
                            font: {
                                family: "'Poppins', sans-serif",
                                weight: 400,
                                size: 12,
                                lineHeight: 1.5
                            }
                        }
                        
                    },
                    title: {
                        display: true,
                        text: 'Total Credits - {{$buyer_credit_left+$buyer_credit_used}}',
                        color: '#000000',
                        font: {
                            size: 12,
                            weight: 600
                        },
                        position: 'right',
                    }
                },
            }
        });
    });
</script>
<script>
    $(document).ready(function() {

    const consumptionChart = new Chart(document.getElementById("seller-creditChart"), {
        type: "pie",
            data: {
                labels: [
                    'Credits Left',
                    'Credits Used'
                ],
                datasets: [{
                    label: '',
                    data: ["{{$seller_credit_left}}", "{{$buyer_credit_used}}"],
                    backgroundColor: [
                        '#30BA00',
                        '#D82C42'
                    ],
                    hoverOffset: 0,
                    borderWidth: 0,
                    maxHeight: 16,
                    maxHeight: 16
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 16,
                            boxHeight: 16,
                            color: '#000000',
                            padding: 20,
                            font: {
                                family: "'Poppins', sans-serif",
                                weight: 400,
                                size: 12,
                                lineHeight: 1.5
                            }
                        }
                        
                    },
                    title: {
                        display: true,
                        text: 'Total Credits - {{$seller_credit_left+$buyer_credit_used}}',
                        color: '#000000',
                        font: {
                            size: 12,
                            weight: 600
                        },
                        position: 'right',
                    }
                },
            }
        });
    });
</script>
@endsection