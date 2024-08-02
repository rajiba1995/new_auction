@extends('front.layout.app')
@section('section')
<style>
.total-row {
    background-color: #c8e9f5 !important;/* Blue background color */
}
.search_btn {
    height: 37px !important;width: 117%!important;margin-top: 2px!important;line-height: 40px !important;
}
.align_div{
    width: 215px !important;
}

</style>
@php
    use Carbon\Carbon;

    // Calculate the default start date (one month before today)
    $defaultStartDate = Carbon::now()->subMonth()->format('Y-m-d');

    // Get the provided start and end dates from the request or set default values
    $startDate = request()->input('start_date') ?? $defaultStartDate;
    $endDate = request()->input('end_date') ?? Carbon::now()->format('Y-m-d');

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
                                    <div class="top-content-bar">
                                        <h5 class="text-light">My Transaction History</h5>
                                        <a href="{{route('user.payment_management')}}" class="btn btn-normal btn-cta"><i class="fa-solid fa-backward"></i>                                              
                                        Back
                                        </a>
                                    </div>
                                    <form action="" method="get" id="">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-lg-auto col-12 mb-2 align_div">
                                                    <label for="start_date">Start date</label>
                                                    <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->input('end_date') ?? $startDate }}">
                                                </div>
                                                <div class="col-lg-auto col-12 mb-2 align_div">
                                                    <label for="end_date">End date</label>
                                                    <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->input('end_date') ?? $endDate }}">
                                                </div>
                                                <div class="col-lg-auto col-12 mb-2 align_div">
                                                    <label for="mode">Mode</label>
                                                    <select name="mode" class="form-control form-control-sm w-100">
                                                        <option value="1" {{ request()->input('mode') == "1" ? 'selected' : '' }}>Online</option>
                                                        <option value="0" {{ request()->input('mode') == "0" ? 'selected' : '' }}>Offline</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-auto col-12 mb-2 align_div">
                                                    <label for="purpose">Purpose</label>
                                                    <select name="purpose" class="form-control form-control-sm w-100">
                                                        <option value="" selected hidden>Purpose</option>
                                                        @if(count($purpose_array)>0)
                                                            @foreach ($purpose_array as $item)
                                                                <option value="Package" {{ request()->input('purpose') == $item ? 'selected' : '' }}>{{$item}}</option>
                                                            @endforeach
                                                        @endif
                                                        
                                                    </select>
                                                </div>
                                                <div class="col-lg-auto col-12 mb-2 align_div text-center" style="margin-top: 23px;">
                                                    <button type="submit" class="btn btn-sm btn-primary search_btn">
                                                        <i class="fa-solid fa-magnifying-glass"></i> Search
                                                    </button>
                                                    {{-- <a href="{{ route('admin.employee.details.export',['start_date'=>request()->input('start_date'),'end_date'=>request()->input('end_date'),'keyword'=>request()->input('keyword')]) }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="Export">Export</a> --}}
                                                </div>
                                                <div class="col-lg-auto col-12 mb-2 align_div text-center" style="margin-top: 23px;">
                                                    <a href="{{route('user.transaction')}}" class="btn btn-danger">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <div class="content-box">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>SL.</th>
                                                    <th>Unique Number</th>          
                                                    <th>Mode</th>
                                                    <th>Purpose</th>
                                                    <th>Transaction Id</th>
                                                    <th>Transaction Source</th>
                                                    <th>Price (₹)</th>
                                                    <th>Purchase Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-middle">
                                                @php
                                                $totalPrice = 0;
                                                @endphp
                                                @forelse ($transactions as $key =>$item)
                                                <tr style="font-size: 12px;">
                                                    <td> {{ $key+1 }}</td>
                                                    <td> {{ $item->unique_id }}</td>
                                                    <td> {{ $item->transaction_type == 1?'Online':'Offline'}}</td>
                                                    <td> {{ $item->purpose }}</td>
                                                    <td> {{ $item->transaction_id??"NULL" }}</td>
                                                    <td> {{ $item->transaction_source }}</td>
                                                    <td>{{ number_format($item->amount, 2) }}</td>
                                                    <td> {{ $item->created_at->format('d-M-Y') }}</td>
                                                </tr>
                                                @php
                                                $totalPrice += $item->amount;
                                                @endphp
                                                @empty
                                                <tr>
                                                    <td colspan="100%" class="text-center">No transaction records found</td>
                                                </tr>
                                                @endforelse
                                                @if ($transactions->isNotEmpty())
                                                <tr>
                                                    <td class="total-row" colspan="5"></td>
                                                    <td class="total-row"><strong>Total:</strong></td>
                                                    <td class="total-row"><strong>{{ number_format($totalPrice, 2) }}</strong></td>
                                                    <td class="total-row" colspan="2"></td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                        {{$transactions->appends($_GET)->links()}}
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
    // Set the max attribute of the end date input to today's date
    document.getElementById('end_date').setAttribute('max', new Date().toISOString().split('T')[0]);
</script>
@endsection