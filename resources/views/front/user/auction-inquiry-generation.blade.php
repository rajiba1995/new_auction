@extends('front.layout.app')
@section('section')
<style>
    .btn-green{
        line-height: 0 !important;
    }
    .message_li{
        list-style-type: none;
    }
    select.select2 {
        width: 300px;
    }
    .img-new-holder{
        width: 124px !important;
        height: 125px !important;
    }
    .add_btn{
        cursor: pointer;
    }
    .search-height {
        max-height: 650px;
        overflow-y: auto;
    }
</style>
<div class="main">
    <div class="inner-page">

        <section class="auction-requirement-section">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-1 col-12"></div>
                    <div class="col-xxl-10 col-12">
                        <div class="inner">
                            <h2>INQUIRY GENERATION FORM</h2>
                            <form action="{{route('front.auction_inquiry_generation_store')}}" class="auction-requirement-form" method="POST" enctype="multipart/form-data" id="auction_requirement_form">
                                @csrf
                                <input type="hidden" name="inquiry_id" value="{{$existing_inquiry?$existing_inquiry->inquiry_id:""}}">
                                <input type="hidden" name="saved_inquiry_id" value="{{$existing_inquiry?$existing_inquiry->id:""}}">
                                <input type="hidden" name="created_by" value="{{$user->id}}">
                                @if($existing_inquiry && $existing_inquiry->inquiry_id)
                                <div class="inquiry-id-row">
                                    <span>Inquiry Id : {{$existing_inquiry->inquiry_id}}</span>
                                </div>
                                @endif
                                <ul>
                                    @if (session('success') || session('warning'))
                                        <li id="message_li"> 
                                            @if (session('success'))
                                                <div class="alert alert-success" id="successAlert">
                                                    {{ session('success') }}
                                                </div>
                                            @endif
                                            @if (session('warning'))
                                                <div class="alert alert-warning" id="successAlert">
                                                    {{ session('warning') }}
                                                </div>
                                            @endif
                                        </li>
                                    @endif
                                </ul>
                                <h4 class="color-red text-center">Title</h4>
                                <div class="row input-row">
                                    <div class="offset-lg-3 col-lg-6 col-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control border-red" placeholder="Ex, transport Service" name="title" value="{{$existing_inquiry ? $existing_inquiry->title : old('title')}}">
                                            @error('title')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <h4 class="color-red">Inquiry Date</h4>
                                <div class="row input-row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Start Date*</label>
                                            <input type="date" class="form-control border-red" name="start_date" value="{{$existing_inquiry ? $existing_inquiry->start_date : old('start_date')}}">
                                            @error('start_date')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">End Date*</label>
                                            <input type="date" class="form-control border-red" name="end_date" value="{{$existing_inquiry ? $existing_inquiry->end_date : old('end_date')}}">
                                            @error('end_date')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <h4 class="color-red">Inquiry Time</h4>
                                <div class="row input-row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Start Time*</label>
                                            <input type="time" class="form-control border-red" name="start_time" value="{{$existing_inquiry ? $existing_inquiry->start_time : old('start_time')}}">
                                            @error('start_time')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">End Time*</label>
                                            <input type="time" class="form-control border-red" name="end_time" value="{{$existing_inquiry ? $existing_inquiry->end_time : old('end_time')}}">
                                            @error('end_time')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row input-row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Category*</label>
                                            <select class="form-control border-red" name="category" id="category">
                                                <option value="" selected hidden>Ex, transport service, Parlour, etc </option>
                                                @foreach($all_category as $key=>$item)
                                                    <option value="{{ $item->title }}" {{ $existing_inquiry && $existing_inquiry->category == $item->title ? 'selected' : '' }}>{{$item->title}}</option>
                                                @endforeach
                                            </select>
                                            @error('category')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Sub-Category*</label>
                                            <select class="form-control border-red" name="sub_category" id="sub_category">
                                                <option value="" selected hidden>Ex, transport service, Parlour, etc </option>
                                                @if($existing_inquiry)
                                                <option value="{{$existing_inquiry->sub_category}}" selected>{{$existing_inquiry->sub_category}}</option>
                                                @endif
                                            </select>
                                            @error('sub_category')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row input-row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Service Description</label>
                                            <textarea class="form-control border-red ckeditor" rows="9" placeholder="Ex, transport service, Parlour, etc " name="description" id="description">{{$existing_inquiry ? $existing_inquiry->description : old('description')}}</textarea>
                                            @error('description')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row input-row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Execution Date*</label>
                                            <input type="date" class="form-control border-red" name="execution_date" value="{{$existing_inquiry ? $existing_inquiry->execution_date : old('execution_date')}}">
                                            @error('execution_date')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">No. of Quotes per Participants*</label>
                                            <input type="number" class="form-control border-red" placeholder="ex, 1, 2, 3 etc" name="quotes_per_participants" value="{{$existing_inquiry ? $existing_inquiry->quotes_per_participants : old('quotes_per_participants')}}">
                                            @error('quotes_per_participants')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $SellerData = [];
                                @endphp
                                @if(!empty($existing_inquiry) && count($existing_inquiry->ParticipantsData) > 0)
                                <div class="row input-row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">Existing Participants*</label>
                                            <div class="participants-block border-red">
                                                @foreach ($existing_inquiry->ParticipantsData as $key=>$item)
                                                    @php
                                                        $SellerData[] = $item->SellerData->id;
                                                    @endphp
                                                    <label class="participant" id="exist_participant{{$item->id}}">
                                                        @if($item->SellerData)
                                                            <input type="hidden" name="exist_participant[]" value="{{$item->SellerData->id}}">
                                                        @endif
                                                        {{$item->SellerData && $item->SellerData->business_name ?$item->SellerData->business_name:""}}
                                                        <span class="Exist_remove remove" data-id="{{$item->id}}">
                                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.3636 3.7738L4.66797 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M4.66797 3.7738L13.3636 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(count($exsisting_outside_participant)>0)
                                    <div class="row input-row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Existing Outside Participants*</label>
                                                <div class="participants-block border-red">
                                                    @foreach ($exsisting_outside_participant as $item)
                                                        <label class="participant" id="participant{{$item->id}}">
                                                                <input type="hidden" name="outside_participant[]" value="{{$item->id}}">
                                                            {{$item->name}}
                                                            <span class="remove exists_outside_participant_remove" data-id="{{$item->id}}">
                                                                <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.3636 3.7738L4.66797 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M4.66797 3.7738L13.3636 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(count($outside_participant_data)>0)
                                    <div class="row input-row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Outside Participants*</label>
                                                <div class="participants-block border-red">
                                                    @foreach ($outside_participant_data as $item)
                                                        <label class="participant" id="participant{{$item->id}}">
                                                                <input type="hidden" name="outside_participant[]" value="{{$item->id}}">
                                                            {{$item->name}}
                                                            <span class="remove outside_participant_remove" data-id="{{$item->id}}">
                                                                <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.3636 3.7738L4.66797 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M4.66797 3.7738L13.3636 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(count($outside_participant_without_group)>0 && $group_id=="")
                                    <div class="row input-row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Outside Participants*</label>
                                                <div class="participants-block border-red">
                                                    @foreach ($outside_participant_without_group as $item)
                                                        <label class="participant" id="participant{{$item->id}}">
                                                                <input type="hidden" name="outside_participant[]" value="{{$item->id}}">
                                                            {{$item->name}}
                                                            <span class="remove outside_participant_remove" data-id="{{$item->id}}">
                                                                <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.3636 3.7738L4.66797 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M4.66797 3.7738L13.3636 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(count($watch_list_data)>0)
                                <div class="row input-row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">New Participants*</label>
                                            <div class="participants-block border-red" id="append_new_partisipants">
                                                @foreach ($watch_list_data as $item)
                                                {{-- {{dd($SellerData)}} --}}
                                                @if(!in_array($item->SellerData->id,$SellerData))
                                                    <label class="participant" id="participant{{$item->id}}">
                                                        @if($item->SellerData)
                                                            <input type="hidden" name="participant[]" value="{{$item->SellerData->id}}">
                                                        @endif
                                                        {{$item->SellerData && $item->SellerData->business_name ?$item->SellerData->business_name:""}}
                                                        <span class="remove Exist_remove2" data-id="{{$item->id}}">
                                                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.3636 3.7738L4.66797 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M4.66797 3.7738L13.3636 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </span>
                                                    </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="add-invite-row">
                                    <button type="button" onclick="checkModal()" id="checkModal" class="btn btn-add-invite" data-bs-toggle="modal" data-bs-target="#inviteModalWebsite">
                                        <svg width="23" height="20" viewBox="0 0 23 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_613_9373)">
                                            <path d="M15.332 17.5V15.8333C15.332 14.9493 14.9282 14.1014 14.2093 13.4763C13.4904 12.8512 12.5154 12.5 11.4987 12.5H4.79036C3.7737 12.5 2.79868 12.8512 2.07979 13.4763C1.3609 14.1014 0.957031 14.9493 0.957031 15.8333V17.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.14583 9.16667C10.2629 9.16667 11.9792 7.67428 11.9792 5.83333C11.9792 3.99238 10.2629 2.5 8.14583 2.5C6.02874 2.5 4.3125 3.99238 4.3125 5.83333C4.3125 7.67428 6.02874 9.16667 8.14583 9.16667Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19.168 6.66666V11.6667" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22.043 9.16666H16.293" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_613_9373">
                                            <rect width="23" height="20" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>                                                
                                        Add Participants from Website
                                    </button>

                                    <button type="button" class="btn btn-add-invite" data-bs-toggle="modal" data-bs-target="#inviteModal">
                                        <svg width="23" height="20" viewBox="0 0 23 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_613_9373)">
                                            <path d="M15.332 17.5V15.8333C15.332 14.9493 14.9282 14.1014 14.2093 13.4763C13.4904 12.8512 12.5154 12.5 11.4987 12.5H4.79036C3.7737 12.5 2.79868 12.8512 2.07979 13.4763C1.3609 14.1014 0.957031 14.9493 0.957031 15.8333V17.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.14583 9.16667C10.2629 9.16667 11.9792 7.67428 11.9792 5.83333C11.9792 3.99238 10.2629 2.5 8.14583 2.5C6.02874 2.5 4.3125 3.99238 4.3125 5.83333C4.3125 7.67428 6.02874 9.16667 8.14583 9.16667Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19.168 6.66666V11.6667" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22.043 9.16666H16.293" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_613_9373">
                                            <rect width="23" height="20" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>                                                
                                        Invite Participants from Outside
                                    </button>
                                </div>
                            
                                <div class="row input-row">
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Maximum Quote Amount</label>
                                            <input type="text" class="form-control border-red" placeholder="Ex,  bid end Rs 30,000" name="maximum_quote_amount" value="{{$existing_inquiry ? $existing_inquiry->maximum_quote_amount : old('maximum_quote_amount')}}">
                                            @error('maximum_quote_amount')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Minimum Quote Amount</label>
                                            <input type="text" class="form-control border-red" placeholder="Ex, bid start Rs 15,000" name="minimum_quote_amount" value="{{$existing_inquiry ? $existing_inquiry->minimum_quote_amount : old('minimum_quote_amount')}}">
                                            @error('minimum_quote_amount')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>                                 
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Bid Difference Quote Amount*</label>
                                            <input type="text" class="form-control border-red" placeholder="Ex, bid difference Rs 50/100/200" name="bid_difference_quote_amount" value="{{$existing_inquiry ? $existing_inquiry->bid_difference_quote_amount : old('bid_difference_quote_amount')}}">
                                            @error('bid_difference_quote_amount')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>                                 
                                </div>

                                <div class="row input-row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="openauction" class="modal-custom-radio">
                                                <input type="radio" name="auction_type" id="openauction" value="open auction" {{ ($existing_inquiry && $existing_inquiry->inquiry_type == "open auction") || (old('auction_type') == "open auction") ? "checked" : "" }}>
                                                <span class="checkmark">
                                                    <span class="checkedmark"></span>
                                                </span>
                                                <div class="radio-text">
                                                    <label for="openauction">Open Inquiry</label>
                                                    <span>Auction where Inquiry can be sent to all businesses on the website</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="closedauction" class="modal-custom-radio">
                                                <input type="radio" name="auction_type" id="closedauction" value="close auction" {{ ($existing_inquiry && $existing_inquiry->inquiry_type == "close auction") || (old('auction_type') == "close auction") ? "checked" : "" }}>
                                                <span class="checkmark">
                                                    <span class="checkedmark"></span>
                                                </span>
                                                <div class="radio-text">
                                                    <label for="closedauction">Closed Inquiry</label>
                                                    <span>Auction where Inquiry is sent to selected participants</span>
                                                    <span class="credit_message text-danger font-weight-bold"></span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        @error('auction_type')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row input-row open-auction-options {{ ($existing_inquiry && $existing_inquiry->inquiry_type == "open auction") || (old('auction_type') == "open auction") ? "show" : "" }}" id="openAuctionOptions">
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="fromcountry" class="modal-custom-radio">
                                                <input type="radio" name="supplier_location" id="fromcountry" value="country" {{ ($existing_inquiry && $existing_inquiry->location_type == "country") || (old('supplier_location') == "country") ? "checked" : "" }}>
                                                <span class="checkmark">
                                                    <span class="checkedmark"></span>
                                                </span>
                                                <div class="radio-text">
                                                    <label for="fromcountry">Suppliers from PAN India</label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="fromcity" class="modal-custom-radio">
                                                <input type="radio" name="supplier_location" id="fromcity" value="city" {{ ($existing_inquiry && $existing_inquiry->location_type == "city") || (old('supplier_location') == "city") ? "checked" : "" }}>
                                                <span class="checkmark">
                                                    <span class="checkedmark"></span>
                                                </span>
                                                <div class="radio-text">
                                                    <label for="fromcity">Choose from my City</label>
                                                </div>
                                            </label>
                                            <input type="hidden" name="city" value="{{$user->city}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="fromregion" class="modal-custom-radio">
                                                <input type="radio" name="supplier_location" id="fromregion" value="region" {{ ($existing_inquiry && $existing_inquiry->location_type == "region") || (old('supplier_location') == "region") ? "checked" : "" }}>
                                                <span class="checkmark">
                                                    <span class="checkedmark"></span>
                                                </span>
                                                <div class="radio-text">
                                                    <label for="fromregion">Choose from Region</label>
                                                </div>
                                            </label>
                                            <select id="selectRegion" class="form-control border-red select-region {{ ($existing_inquiry && $existing_inquiry->location_type == "region") || (old('supplier_location') == "region") ? "show" : "" }}" name="region">
                                                <option value="" selected hidden>Select Region</option>
                                                @foreach ($States as $item)
                                                    <option value="{{$item->name}}" {{ $existing_inquiry && $existing_inquiry->location == $item->name ? 'selected' : '' }}>{{$item->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        @error('supplier_location')<span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-save-row">
                                    <button type="submit" class="btn btn-animated btn-green btn-save" data-value="save">Save Inquiry</button>
                                </div>
                                @if($existing_inquiry && !$existing_inquiry->inquiry_id)
                                    <div class="form-submit-row">
                                        <button type="submit" class="btn btn-animated btn-submit" id="generate_btn" data-value="generate">GENERATE INQUIRY</button>
                                    </div>
                                @elseif(empty($existing_inquiry))
                                    <div class="form-submit-row">
                                        <button type="submit" class="btn btn-animated btn-submit" id="generate_btn" data-value="generate">GENERATE INQUIRY</button>
                                    </div>
                                @endif
                                <input type="hidden" id="submit_type" name="submit_type">
                            </form>
                        </div>
                    </div>
                    <div class="col-xxl-1 col-12"></div>
                </div>
            </div>
        </section>
        
    </div>
</div>

   {{-- invite modal from out side --}}
   <div class="modal fade invite-modal" id="inviteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Invite Participants from Outside</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="inviteForm" >
                    @csrf
                    <div class="input-row">
                            <div class="input-wrap">
                                <input type="hidden" name="groupId" value="{{$group_id}}">
                                <label>Name</label>
                                <input type="text" name="name[]" placeholder="Ex, John" class="border-red" required>
                            </div>
                            <div class="input-wrap">
                                <label>Phone Number *</label>
                                <input type="text" name="phone[]" placeholder="+91 xx - xxx - xxxx" class="border-red" required>
                            </div>
                        <button type="button" class="btn-add" id="addMore">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 12H19" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        {{-- <button type="button" class="btn-remove">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6H5H21" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button> --}}
                    </div>
                    <div id="AppendData" class="mt-2"></div>
                    <button type="button" class="btn btn-animated btn-submit" onclick="submitIviteForm()">Add Now</button>
                </form>
            </div>
      
        </div>
    </div>
</div>

   {{-- invite modal from website --}}
   <div class="modal fade show" id="inviteModalWebsite" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Invite Participants from Website</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="search-section">
                            <div class="location-bar location_bar" style="width: 32% !important;">
                                <img src="{{asset('frontend/assets/images/location.png')}}" alt="">
                                <input type="text" placeholder="Select Location" id="location_input" name="global_state_name" autocomplete="off" value="west bengal">
                            </div>
                            <div id="stateSuggestions"></div>
                            
                            <div class="search-bar search_bar">
                                <input type="search" name="keyword" id="partisipants_location" placeholder="Search" autocomplete="off">
                            </div>
                             <!-- <div id="filterSuggestions"></div> -->
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12 col-12 left-col">
                            <div id="append_website_partisipant" class="search-height">

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
    $(document).ready(function(){
        $('#auction_requirement_form button[type="submit"]').click(function(event){
            event.preventDefault();
            
            var participantsInside = $('[name="participant[]"]').length;
            var exist_participant = $('[name="exist_participant[]"]').length;
            var participantsOutside = $('[name="outside_participant[]"]').length;
            var totalCount = participantsInside + participantsOutside+exist_participant;
            var submitType = $(this).data('value');
            $('#submit_type').val(submitType); 
            // Set the value of the hidden input field based on the clicked button
            var closedauction = $('input[name="auction_type"]:checked').val();
            var buyer_active_credit = "{{$buyer_active_credit}}";
            if(buyer_active_credit==0){
                Swal.fire({
                    title: "Warning!",
                    text: "You don't have active package or wallet balance.!",
                    icon: "warning"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/seller/set-session-and-redirect',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}', // Laravel CSRF token
                                type: 'buyer',
                                intended_url: window.location.href // Current URL
                            },
                            success: function(response) {
                                toastr.error(response.error);
                                if (response.redirect) {
                                    window.location.href = response.redirect_url;
                                } else {
                                    // Handle the error message
                                    toastr.error(response.error);
                                }
                            }
                        });
                    }
                });
                return false;
            }
            if (closedauction == "close auction") {
                if(submitType=="generate"){
                    if(totalCount==0){
                        Swal.fire({
                            title: "Warning!",
                            text: "Please select a participant for this inquiry!",
                            icon: "warning"
                        });
                        return false;
                    }else{
                        Swal.fire({
                            title: "Warning!",
                            text: "Credit will be used!",
                            icon: "warning"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var button = $('#generate_btn');
                                button.prop('disabled', true); // Disable the button
                                button.text('Please wait...');
                                $('.page-loader').fadeIn('fast');
                                $('#auction_requirement_form').submit(); // Submit the form
                            }
                        });
                    }
                }else{
                    $('.page-loader').fadeIn('fast');
                    $('#auction_requirement_form').submit(); // Submit the form
                }
            } else{
                if(totalCount>0 && submitType=="generate"){
                    Swal.fire({
                        title: "Warning!",
                        text: "Credit will be used for selected participants!",
                        icon: "warning"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('.page-loader').fadeIn('fast');
                            var button = $('#generate_btn');
                            button.prop('disabled', true); // Disable the button
                            button.text('Please wait...');
                            $('#auction_requirement_form').submit(); // Submit the form
                        }
                    });
                }else{
                    $('.page-loader').fadeIn('fast');
                    $('#auction_requirement_form').submit(); // Submit the form
                }
            }
           
        });
    });
     $("input[name='auction_type']").click(function() {
            var inputval = $(this).val();
            if(inputval == "open auction") {
                $("#openAuctionOptions").addClass('show');
               
            } else {
                $("#openAuctionOptions").removeClass('show');
            }
    });

    $(document).ready(function () {
        $('.Exist_remove2').click(function () {
            var itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route("user.single_watchlist.delete") }}',
                        data: {
                            id:itemId
                        },
                        success: function(response) {
                            if(response.status==200){
                                $('#participant' + itemId).empty();
                                $('#participant' + itemId).removeClass('participant');
                                // Swal.fire({
                                //     title: "Success!",
                                //     text: "Participant has been deleted successfully!",
                                //     icon: "success"
                                // });
                                // setTimeout(function() {
                                //     // Reload the page
                                //     location.reload();
                                // }, 1000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
    $(document).ready(function () {
        $('.outside_participant_remove').click(function () {
            var itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route("user.outside_participant.delete") }}',
                        data: {
                            id:itemId
                        },
                        success: function(response) {
                            if(response.status==200){
                                $('#participant' + itemId).empty();
                                $('#participant' + itemId).removeClass('participant');
                                // Swal.fire({
                                //     title: "Success!",
                                //     text: "Outside Participant has been deleted successfully!",
                                //     icon: "success"
                                // });
                                // setTimeout(function() {
                                //     // Reload the page
                                //     location.reload();
                                // }, 1000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
    $(document).ready(function () {
        $('.exists_outside_participant_remove').click(function () {
            var itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route("user.exsists_outside_participant.delete") }}',
                        data: {
                            id:itemId
                        },
                        success: function(response) {
                            if(response.status==200){
                                $('#participant' + itemId).empty();
                                $('#participant' + itemId).removeClass('participant');
                                // Swal.fire({
                                //     title: "Success!",
                                //     text: "Existing Outside Participant has been deleted successfully!",
                                //     icon: "success"
                                // });
                                // setTimeout(function() {
                                //     // Reload the page
                                //     location.reload();
                                // }, 1000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
    $(document).ready(function () {
        $('.Exist_remove').click(function () {
            var itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route("front.auction_participants_delete") }}',
                        data: {
                            id:itemId
                        },
                        success: function(response) {
                            $('#exist_participant' + itemId).empty();
                            $('#exist_participant' + itemId).removeClass('participant');
                            if(response.status==200){
                                // Swal.fire({
                                //     title: "Success!",
                                //     text: "Participant has been deleted successfully!",
                                //     icon: "success"
                                // });
                                setTimeout(function() {
                                    // Reload the page
                                    location.reload();
                                }, 1000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
    $(document).ready(function() {
        $('select[name="category"]').change(function(){
            var selectedCategory = $(this).val();
            // Perform an AJAX request to fetch sub-categories based on the selected category
            $.ajax({
                url: "{{route('user.collection_wise_category_by_title')}}", // Replace this with your actual route
                type: 'GET',
                data: {category: selectedCategory},
                success: function(response) {
                    if(response.status==200){
                        // Clear existing options before appending new ones
                        $('select[name="sub_category"]').html("");
                        var isFirst = true;
                        // Append new options based on the response data
                        response.data.forEach(function(element) {
                            var option = '<option value="' + element.title + '">' + element.title + '</option>';
                            // Check the first option
                            if (isFirst) {
                                option = '<option value="' + element.title + '" selected>' + element.title + '</option>';
                                isFirst = false; // Reset the flag after the first iteration
                            }
                            // Append the option to the select element
                            $('select[name="sub_category"]').append(option);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Handle errors if any
                }
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-1.11.3.js"></script>
    <script>
        $(document).ready(function() {
            $("#addMore").click(function() {
                $('<div class="input-row">' +
                    '<div class="input-wrap">' +
                    '<label>Name</label>' +
                    '<input type="text" name="name[]" placeholder="Ex, John" class="border-red">' +
                    '</div>' +
                    '<div class="input-wrap">' +
                    '<label>Phone Number *</label>' +
                    '<input type="text"  name="phone[]" placeholder="Ex xx - xxx - xxxx" class="border-red">' +
                    '</div>' +
                    '<button type="button" class="btn-remove">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">' +
                    '<path d="M3 6H5H21" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<path d="M10 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '<path d="M14 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>' +
                    '</svg>' +
                    '</button>' +
                    '</div>').insertAfter(".input-row:last");
            });
        
            $('body').on('click', '.btn-remove', function() {
                $(this).closest('.input-row').remove();
            });
        });

 </script>
  <script>
    function submitIviteForm(){
        event.preventDefault();
        // var formData = new FormData(document.getElementById('inviteForm'));
        var formData = $('#inviteForm').serialize();       
        $.ajax({
            type: "POST",
            url: ' {{route("user.invite_outside_participants.store") }} ',
            data: formData,
            success: function(response) {
                if(response.status==200){
                    location.reload();
                }
                if(response.status==400){
                    const alertHtml = `
                        <div class="alert alert-danger" role="alert">
                            ${response.error}
                        </div>
                    `;
                    $('#AppendData').html(alertHtml);
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 3000); 
                }
                
            },
            error: function(xhr, status, error) {
                // Handle error
                console.log(xhr.responseText);
            }
        });

    }
</script>
<script src="{{asset('frontend/assets/js/custom.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function checkModal(){
        var category =$('#category').val().trim();
        var subCategory =$('#sub_category').val().trim();
        // if(category.length === 0  &&  subCategory.length === 0){
        //     Swal.fire("Please select Category & Sub-Category before adding participants");
        //     return false;
        // }else{
            $('#inviteModalWebsite').modal('show');
        // }
    }
    // Assuming you have a button or some trigger to call this function
    document.getElementById('checkModal').addEventListener('click', checkModal);
});
    document.addEventListener('DOMContentLoaded', function() {
        $('#partisipants_location').on('keyup', function() {
            var keyword = $('#partisipants_location').val().toLowerCase();
            var location = $('#location_input').val();
            if (!keyword) {
                $('.search_bar ').css('border', '1px solid red');
                $('#append_website_partisipant').html("");
                return false;
            }
                    // Reset border to default
            $('.location_bar').css('border', '1px solid #ced4da');
            $('.search_bar ').css('border', '1px solid #ced4da');
            
            // Check if location is empty
            if(location.trim().length === 0){
                $('.location_bar').css('border', '1px solid red');
                return; // Stop further execution  
            }
            $.ajax({
                url: "{{route('user.filter_partisipants_from_website')}}", // Replace this with your actual route
                type: 'GET',
                data: {
                    location: location,
                    keyword: keyword
                },
                success: function(response) {
                    if(response.status=='success'){
                        var result_data = response.master_date.data;
                        var html= "";
                        result_data.forEach(function(element, index) {
                            let imageSrc = element.image ? "{{asset("+element.image+")}}" : "{{asset('frontend/assets/images/building.png')}}";
                            let add_img = "{{asset('frontend/assets/images/add.png')}}";
                            let mobile = element.mobile ? element.mobile : "xxx";
                            let maskedNumber = mobile.replace(/.(?=.{3,}$)/g, 'x');
                            html += `<div class="bidder-box">
                                <div class="dots-cta">
                                    <div class="dropdown add_btn" data-id="${element.id}" onclick="add_partisipants(${element.id}, '${element.business_name}')">
                                        <img src="${add_img}" alt="" class="participant-img"">
                                    </div>
                                </div>
                                <div class="img-holder img-new-holder">
                                    <img src="${imageSrc}" alt="">
                                </div>
                                <div class="content-holder">
                                    <div class="approvals">
                                        <ul>`;
                                            if(element.verified_badge==true){
                                            html += `<li>
                                                <img src="${element.verified_badge_logo}" alt="">
                                                <div class="infotip"><span>${element.verified_badge_short_desc}</span></div>
                                            </li>`;
                                            }
                                            if(element.trusted_badge_logo){
                                            html += `<li>
                                                <img src="${element.trusted_badge_logo}" alt="">
                                                <div class="infotip"><span>${element.trusted_badge_short_desc}</span></div>
                                            </li>`;
                                            }
                                            var paid_badge_list = element.paid_badge_list;
                                            paid_badge_list.forEach(function(badge, key) {
                                            html += `<li>
                                                <img src="${badge.logo}" alt="">
                                                <div class="infotip"><span>${badge.desc}</span></div>
                                            </li>`;
                                            });
                                        html += `</ul>
                                    </div>
                                    <div class="name">${element.business_name}</div>
                                    <div class="rating-value mb-2">
                                        <div class="rating-star-values">
                                            <span class="badge badge-rating bg-theme">${element.seller_over_all_rating}</span>`;
                                        if(element.seller_over_all_rating>0){
                                            html+=`<ul class="rating-stars solid-stars" data-rating="${element.seller_over_all_rating}">
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                            </ul>`;
                                        }else{
                                            html+=`<ul class="rating-stars blank-stars">
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                                <li>
                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m29.911 13.75-6.229 6.072 1.471 8.576a1 1 0 0 1-1.451 1.054L16 25.403l-7.701 4.048a1 1 0 0 1-1.451-1.054l1.471-8.576-6.23-6.071a1 1 0 0 1 .555-1.706l8.609-1.25 3.85-7.802c.337-.683 1.457-.683 1.794 0l3.85 7.802 8.609 1.25a1.002 1.002 0 0 1 .555 1.706z" fill="#000000" opacity="1" data-original="#000000" class="" style="
                                                        "></path></g></svg>
                                                </li>
                                            </ul>`;
                                        }
                                        html +=`</div>
                                        
                                    </div>
                                    <div class="info">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        ${element.address}, ${element.city_data.name}, ${element.state_data.name}
                                    </div>
                                    <div class="info">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M15.0499 5C16.0267 5.19057 16.9243 5.66826 17.628 6.37194C18.3317 7.07561 18.8094 7.97326 18.9999 8.95M15.0499 1C17.0792 1.22544 18.9715 2.13417 20.4162 3.57701C21.8608 5.01984 22.7719 6.91101 22.9999 8.94M21.9999 16.92V19.92C22.0011 20.1985 21.944 20.4742 21.8324 20.7293C21.7209 20.9845 21.5572 21.2136 21.352 21.4019C21.1468 21.5901 20.9045 21.7335 20.6407 21.8227C20.3769 21.9119 20.0973 21.9451 19.8199 21.92C16.7428 21.5856 13.7869 20.5341 11.1899 18.85C8.77376 17.3147 6.72527 15.2662 5.18993 12.85C3.49991 10.2412 2.44818 7.27099 2.11993 4.18C2.09494 3.90347 2.12781 3.62476 2.21643 3.36162C2.30506 3.09849 2.4475 2.85669 2.6347 2.65162C2.82189 2.44655 3.04974 2.28271 3.30372 2.17052C3.55771 2.05833 3.83227 2.00026 4.10993 2H7.10993C7.59524 1.99522 8.06572 2.16708 8.43369 2.48353C8.80166 2.79999 9.04201 3.23945 9.10993 3.72C9.23656 4.68007 9.47138 5.62273 9.80993 6.53C9.94448 6.88792 9.9736 7.27691 9.89384 7.65088C9.81408 8.02485 9.6288 8.36811 9.35993 8.64L8.08993 9.91C9.51349 12.4135 11.5864 14.4864 14.0899 15.91L15.3599 14.64C15.6318 14.3711 15.9751 14.1858 16.3491 14.1061C16.723 14.0263 17.112 14.0555 17.4699 14.19C18.3772 14.5286 19.3199 14.7634 20.2799 14.89C20.7657 14.9585 21.2093 15.2032 21.5265 15.5775C21.8436 15.9518 22.0121 16.4296 21.9999 16.92Z" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        +91-${maskedNumber}
                                    </div> 
                                </div>
                            </div>`;
                        });
                        $('#append_website_partisipant').html(html);
                    }
                },
                
                error: function(xhr, status, error) {
                    console.error(error);
                    // Handle errors if any
                }
            });
        });
    });

    function add_partisipants(id, business_name) {
        const $element = $(`[data-id="${id}"]`);
        const $img = $element.find('.participant-img');
        const currentSrc = $img.attr('src');

        const addImg = "{{asset('frontend/assets/images/add.png')}}";
        const checklistImg = "{{asset('frontend/assets/images/checklist.png')}}";

        if (currentSrc === addImg) {
            $.ajax({
                type: 'GET',
                url: '{{ route("user.single_watchlist.add") }}',
                data: {
                    seller_id:id
                },
                success: function(response) {
                    if(response.status==200){
                        var html= "";
                        html+=`<label class="participant" id="participant${response.item_id}">
                            <input type="hidden" name="participant[]" value="${id}">
                            <input type="hidden" id="watchlist${id}" value="${response.item_id}">
                                ${business_name}
                                <span class="remove Exist_remove2" data-id="${response.item_id}">
                                    <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.3636 3.7738L4.66797 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M4.66797 3.7738L13.3636 11.2932" stroke="#ee2737" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                            </label>`;
                            $('#append_new_partisipants').append(html);
                            $img.attr('src', checklistImg);
                            $element.addClass('selected');
                            toastr.success('Participant has been successfully added!');
                    }
                    if(response.status==400){
                        toastr.warning('this seller already exists');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        } else {
            var watchlist_id = $('#watchlist'+id).val();
            $.ajax({
                type: 'GET',
                url: '{{ route("user.single_watchlist.delete") }}',
                data: {
                    id:watchlist_id
                },
                success: function(response) {
                    if(response.status==200){
                        $('#participant' + watchlist_id).empty();
                        $('#participant' + watchlist_id).removeClass('participant');
                        toastr.success('Participant has been successfully deleted!');
                        $img.attr('src', addImg);
                        $element.removeClass('selected');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
            
        }
        
        
    }
   
</script>
<script type="text/javascript" src="{{ asset('frontend/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/ckeditor/adapters/jquery.js') }}"></script>
@endsection