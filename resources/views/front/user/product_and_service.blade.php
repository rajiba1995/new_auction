@extends('front.layout.app')
@section('section')
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
                                @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id==$data->id)
                                <div class="tab-pane {{ (request()->is('my/product-and-service*')) ? 'active' : '' }}" id="productsServices" role="tabpanel" aria-labelledby="productsServices-tab" tabindex="0">
                                @else
                                <div class="tab-pane {{ (request()->is('product-and-service/*')) ? 'active' : '' }}" id="productsServices" role="tabpanel" aria-labelledby="productsServices-tab" tabindex="0">
                                    @endif
                                    <div class="tab-content-wrapper">
                                        <div class="top-content-bar">
                                            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id==$data->id)
                                            <a href="{{route('user.product_and_service.add')}}" class="btn btn-normal btn-cta">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="20" height="20" fill="#0076D7"/>
                                                    <path d="M10 4.1665V15.8332" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M4.16602 10H15.8327" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>                                                        
                                                Add Products & Services
                                            </a>
                                            @endif
                                        </div>
                                        <div class="content-box">
                                            <div class="m-2">
                                                {{-- @if (session('success'))
                                                    <div class="alert alert-success" id="message_div">
                                                        {{ session('success') }}
                                                    </div>
                                                @endif --}}
                                            </div>
                                            <div class="inner">
                                                <div class="page-tabs-row">
                                                    <ul class="nav nav-tabs watchlist-tabs" id="productsServicesTab" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="true">Products</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">Services</button>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="tab-content products-services-tab-content">
                                                    <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab" tabindex="0">
                                                        @foreach ($Product as $key=>$item)
                                                            @if($item->type=="Product")
                                                                <div class="prod-serv-box">
                                                                    <div class="cta-box">
                                                                        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id==$data->id)
                                                                       
                                                                            <button type="button" class="btn-remove" data-id="{{$item->id}}">
                                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M3 6H5H21" stroke="#F70000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                    <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="#F70000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                    <path d="M10 11V17" stroke="#F70000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                    <path d="M14 11V17" stroke="#F70000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                </svg>
                                                                            </button>
                                                                        <a href="{{route('user.product_and_service.edit', $item->id)}}" class="btn-cta">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                            <img src="{{asset($item->image)}}" alt="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <h4>{{ucwords($item->title)}}</h4>
                                                                        <p class="desc">
                                                                            {{Str::limit($item->description, 300)}}
                                                                        </p>
                                                                        <div class="row">
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Category:</label>
                                                                                <p>{{$item->CatData?$item->CatData->title:""}}</p>
                                                                            </div>
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Sub-Category:</label>
                                                                                <p>{{$item->SubCatData?$item->SubCatData->title:""}}</p>
                                                                            </div>
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Price per unit:</label>
                                                                                <p>{{$item->price}}</p>
                                                                            </div>
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Product Specification:</label>
                                                                                <p>{{Str::limit($item->specifications, 100)}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab" tabindex="0"> 
                                                        @foreach ($Product as $key=>$item)
                                                            @if($item->type=="Service")
                                                                <div class="prod-serv-box">
                                                                    <div class="cta-box">
                                                                        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id==$data->id)
                                                                        <a href="{{route('user.product_and_service.edit', $item->id)}}" class="btn-cta">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                            <img src="{{asset($item->image)}}" alt="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <h4>{{ucwords($item->title)}}</h4>
                                                                        <p class="desc">
                                                                            {{Str::limit($item->description, 300)}}
                                                                        </p>
                                                                        <div class="row">
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Category:</label>
                                                                                <p>{{$item->CatData?$item->CatData->title:""}}</p>
                                                                            </div>
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Sub-Category:</label>
                                                                                <p>{{$item->SubCatData?$item->SubCatData->title:""}}</p>
                                                                            </div>
                                                                            <div class="col-md-6 col-12 info-col">
                                                                                <label>Price per unit:</label>
                                                                                <p>{{$item->price}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
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
</div>
@endsection
@section('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.btn-remove').click(function() {
            var itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'product-and-service/delete/' + itemId; // Replace '/delete/' with your actual delete route
                }
            });
        });
    });
</script>
<script>
    setTimeout(function() {
        $('#message_div').remove();
    }, 5000);
</script>
@endsection