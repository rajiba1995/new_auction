@extends('front.layout.app')
@section('section')
<style>
   .inner-wrap {
    display: flex;
    align-items: center;
}

.left-col {
    flex: 0 0 auto;
}

.right-col {
    flex: 1 1 auto;
    margin-left: 10px;
}

. {
    display: flex;
    align-items: center;
}
.left-col{
    width: auto !important;
}


</style>
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
                                        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id == $data->id)
                                            <a href="{{ route('user.photos_and_documents_edit') }}" class="btn btn-normal btn-cta">Upload Photos & Documents</a>
                                        @endif
                                    </div>
                                    <div class="content-box">
                                        <div class="inner">
                                            <h3>Photos</h3>
                                            <div class="photos">
                                                @foreach($AllImages as $key => $item)
                                                    <div class="photo-box">
                                                        <img src="{{ asset($item->image) }}" alt="">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id == $data->id)
                                        <div class="content-box">
                                            <div class="inner">
                                                <h3>Documents</h3>
                                                <div class="docs">
                                                    <div class="row">
                                                        @if($user_document && $user_document->gst_file)
                                                            <div class="col-xxl-4 col-md-6 col-12 doc-box">
                                                                <div class="inner-wrap">
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                            @php
                                                                                $filePath = $user_document? asset($user_document->gst_file) : '';
                                                                                $fileExtension = pathinfo($filePath,PATHINFO_EXTENSION);
                                                                            @endphp
                                                                            @if (in_array($fileExtension,['jpg','jpeg','png','gif','jfif']))
                                                                                <img src="{{ asset($filePath) }}" alt="">
                                                                            @elseif($fileExtension === 'pdf')    
                                                                            <div class="">
                                                                                <a href="{{ $filePath }}" target="_blank" class="btn btn-sm btn-animated">View PDF</a>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <label>
                                                                            GST
                                                                            <span>
                                                                                @if($user_document->gst_status == 2)
                                                                                    <img src="{{ asset('frontend/assets/images/failed.png') }}" alt="">
                                                                                @elseif($user_document->gst_status == 1)
                                                                                    <img src="{{ asset('frontend/assets/images/authorized.png') }}" alt="">
                                                                                @endif
                                                                            </span>
                                                                        </label>
                                                                        <p>{{ $user_document->gst_number }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($user_document && $user_document->pan_file)
                                                            <div class="col-xxl-4 col-md-6 col-12 doc-box">
                                                                <div class="inner-wrap">
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                            @php
                                                                                $filePath = $user_document? asset($user_document->pan_file) : '';
                                                                                $fileExtension = pathinfo($filePath,PATHINFO_EXTENSION);
                                                                            @endphp
                                                                            @if (in_array($fileExtension,['jpg','jpeg','png','gif','jfif']))
                                                                                <img src="{{ asset($filePath) }}" alt="">
                                                                            @elseif($fileExtension === 'pdf')    
                                                                            <div class="">
                                                                                <a href="{{$filePath}}" target="blank" class="btn btn-sm btn-animated">View PDF</a>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <label>
                                                                            PAN
                                                                            <span>
                                                                                @if($user_document->pan_status == 2)
                                                                                    <img src="{{ asset('frontend/assets/images/failed.png') }}" alt="">
                                                                                @elseif($user_document->pan_status == 1)
                                                                                    <img src="{{ asset('frontend/assets/images/authorized.png') }}" alt="">
                                                                                @endif
                                                                            </span>
                                                                        </label>
                                                                        <p>{{ $user_document->pan_number }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($user_document && $user_document->adhar_file)
                                                            <div class="col-xxl-4 col-md-6 col-12 doc-box">
                                                                <div class="inner-wrap">
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                            @php
                                                                                $filePath = $user_document? asset($user_document->adhar_file) : '';
                                                                                $fileExtension = pathinfo($filePath,PATHINFO_EXTENSION);
                                                                            @endphp
                                                                            @if (in_array($fileExtension,['jpg','jpeg','png','gif','jfif']))
                                                                                <img src="{{ asset($filePath) }}" alt="">
                                                                            @elseif($fileExtension === 'pdf')    
                                                                            <div class="">
                                                                                <a href="{{$filePath}}" target="blank" class="btn btn-sm btn-animated">View PDF</a>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <label>
                                                                            AADHAR
                                                                            <span>
                                                                                @if($user_document->adhar_status == 2)
                                                                                    <img src="{{ asset('frontend/assets/images/failed.png') }}" alt="">
                                                                                @elseif($user_document->adhar_status == 1)
                                                                                    <img src="{{ asset('frontend/assets/images/authorized.png') }}" alt="">
                                                                                @endif
                                                                            </span>
                                                                        </label>
                                                                        <p>{{ $user_document->adhar_number }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($user_document && $user_document->trade_license_file)
                                                            <div class="col-xxl-4 col-md-6 col-12 doc-box">
                                                                <div class="inner-wrap">
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                        @php
                                                                            $filePath = $user_document? asset($user_document->trade_license_file) : '';
                                                                            $fileExtension = pathinfo($filePath,PATHINFO_EXTENSION);
                                                                        @endphp
                                                                        @if (in_array($fileExtension,['jpg','jpeg','png','gif','jfif']))
                                                                            <img src="{{ asset($filePath) }}" alt="">
                                                                        @elseif($fileExtension === 'pdf')    
                                                                            <a href="{{$filePath}}" target="blank" class="btn btn-sm btn-animated">View PDF</a>
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <label>
                                                                            Trade License
                                                                            <span>
                                                                                @if($user_document->trade_license_status == 2)
                                                                                    <img src="{{ asset('frontend/assets/images/failed.png') }}" alt="">
                                                                                @elseif($user_document->trade_license_status == 1)
                                                                                <div class="">
                                                                                    <img src="{{ asset('frontend/assets/images/authorized.png') }}" alt="">
                                                                                </div>
                                                                                @endif
                                                                            </span>
                                                                        </label>
                                                                        <p>{{ $user_document->trade_license_number }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($user_document && $user_document->cancelled_cheque_file)
                                                            <div class="col-xxl-4 col-md-6 col-12 doc-box">
                                                                <div class="inner-wrap">
                                                                    <div class="left-col">
                                                                        <div class="img-box">
                                                                        @php
                                                                            $filePath = $user_document? asset($user_document->cancelled_cheque_file) : '';
                                                                            $fileExtension = pathinfo($filePath,PATHINFO_EXTENSION);
                                                                        @endphp
                                                                        @if (in_array($fileExtension,['jpg','jpeg','png','gif','jfif']))
                                                                            <img src="{{ asset($filePath) }}" alt="no-image" width="85px" >
                                                                        @elseif($fileExtension === 'pdf')  
                                                                        <div class="">  
                                                                            <a href="{{$filePath}}" target="blank" class="btn btn-sm btn-animated">View PDF</a>
                                                                        </div>
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-col">
                                                                        <label>
                                                                            Cheque
                                                                            <span>
                                                                                @if($user_document->cancelled_cheque_status == 2)
                                                                                    <img src="{{ asset('frontend/assets/images/failed.png') }}" alt="">
                                                                                @elseif($user_document->cancelled_cheque_status == 1)
                                                                                    <img src="{{ asset('frontend/assets/images/authorized.png') }}" alt="">
                                                                                @endif
                                                                            </span>
                                                                        </label>
                                                                        <p>Account Number: {{ $user_document->account_number }}</p>
                                                                        <p>IFSC Code: {{ $user_document->ifsc_code }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="content-box">
                                            <div class="inner">
                                                <h3>Additional Documents</h3>
                                                <div class="docs">
                                                    <div class="row">
                                                       
                                                        @foreach($user_additional_document as $additionalDocument)
                                                            @if($additionalDocument && $additionalDocument->additional_documents)
                                                                <div class="col-xxl-3 col-md-6 col-12 doc-box card p-4" id="RemoveLI{{$additionalDocument->id}}">
                                                                    <div class="inner-wrap">
                                                                        <div class="left-col">
                                                                            <div class="img-box">
                                                                                @php
                                                                                    $filePath = $additionalDocument ? asset($additionalDocument->additional_document_file) : '';
                                                                                    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                                                @endphp
                                                                                @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                                    <img src="{{ $filePath }}" alt="">
                                                                                   
                                                                                    <div class="right-col">
                                                                                        <label>
                                                                                            {{ $additionalDocument->additional_documents }}
                                                                                        </label>
                                                                                    </div>
                                                                                @elseif($fileExtension === 'pdf')
                                                                                    <a href="{{ $filePath }}" target="_blank" class="btn btn-sm btn-animated">View PDF</a>
                                                                                    <div class="right-col">
                                                                                        <label>
                                                                                            {{ $additionalDocument->additional_documents }}
                                                                                        </label>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            
                                                                        </div>
                                                                        <div class="right-col">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style="background: #c35850;border-radius: 3px; cursor:pointer" class="btn-remove remove itemremove" data-id="{{$additionalDocument->id}}">
                                                                                        <path d="M3 6H5H21" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                        <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                        <path d="M10 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                        <path d="M14 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                    </svg> 
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
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
@endsection
@section('script')

<script>
     $('.itemremove').on("click", function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Trigger AJAX request to delete item
                $.ajax({
                    url: "{{route('user.additional_photos_and_documents_delete')}}", // Replace 'your_delete_endpoint' with your actual delete route
                    method: 'GET', // Adjust the HTTP method if needed
                    data: { id: id }, // Pass the item ID to be deleted
                    success: function(response) {
                        // Handle success response
                        $('#RemoveLI'+id).remove();
                        Swal.fire('Deleted!', 'Your item has been deleted.', 'success');
                        // You may want to perform additional actions here like removing the item from the DOM
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        Swal.fire('Error!', 'An error occurred while deleting the item.', 'error');
                    }
                });
            } 
        });
    });
</script>
@endsection