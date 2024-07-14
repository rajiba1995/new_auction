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
                                <div class="tab-content-wrapper">
                                    <div class="top-content-bar">
                                        <p class="text-light">UPLOAD<strong>->YOUR PHOTOS &amp; DOCUMENTS</strong> </p>
                                        <a href="{{ route('user.photos_and_documents') }}" class="btn btn-normal btn-cta"><i class="fa-solid fa-backward"></i> Back</a>
                                    </div>
                                    <div class="inner">
                                            {{-- @if (session('success'))
                                                <div class="alert alert-success" id="message_div">
                                                    {{ session('success') }}
                                                </div>
                                            @endif --}}
                                        <form action="{{ route('user.photos_and_documents_update') }}" class="input-form" method="post" enctype="multipart/form-data">
                                            @csrf   
                                            <div class="upload-photos-block">
                                                <label for="user_images" class="custom-upload">
                                                    <input type="file" name="user_images[]" id="user_images"  multiple="multiple">
                                                <span class="btn btn-animated btn-upload">Upload Photos</span>
                                                </label>
                                                <div class="mt-4">
                                                    @unless ($errors->has('gst_file') || $errors->has('gst_number') || $errors->has('pan_file') || $errors->has('pan_number') || $errors->has('adhar_file') || $errors->has('adhar_number') || $errors->has('trade_license_file') || $errors->has('trade_license_number') || $errors->has('cancelled_cheque_file') || $errors->has('account_number') || $errors->has('ifsc_code'))
                                                            <ul>
                                                                @foreach ($errors->all() as $error)
                                                                <div class="alert alert-danger p-1">
                                                                    <li style="list-style-type: none;">{{ $error }}</li>
                                                                </div>
                                                                @endforeach
                                                            </ul>
                                                    @endunless
                                                </div>
                                                <div class="photos-block">
                                                    @foreach ($AllImages as  $item)
                                                    <div class="photo-wrap" id="RemoveLI{{$item->id}}">
                                                        <img src="{{asset($item->image)}}" alt="No-Image">
                                                            <span class="remove itemremove" data-id="{{$item->id}}">
                                                                <svg width="13" height="11" viewBox="0 0 13 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.5486 3.03894L3.26562 8.21932" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M3.26562 3.03894L9.5486 8.21932" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>                                                        
                                                            </span>
                                                    </div>
                                                    @endforeach                                                    
                                                </div>
                                            </div>
                                            <div class="row input-row align-items-center">
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Upload GST</label>
                                                        <label for="gst_file" class="custom-upload">
                                                            <input type="file" name="gst_file" id="gst_file">
                                                            <span class="btn btn-animated btn-upload">Upload</span>
                                                        </label>
                                                        @error('gst_file')
                                                        <span class="text-danger" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="gstFilePreviewColumn" class="col-lg-4 col-12 text-center" style="display: none;">
                                                    <div id="gstFilePreviewContainer">
                                                        <img id="gstFilePreview" src="" alt="File Preview" class="img-fluid" style="display: none;" width="85px">
                                                        <button id="viewFileButton" class="btn btn-primary" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">GST Number</label>
                                                        <input type="text" name="gst_number" class="form-control {{$user_document && $user_document->gst_number?"border-red":"border-danger"}}" value="{{$user_document?$user_document->gst_number:old('gst_number')}}">
                                                        @error('gst_number')<span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row input-row align-items-center">
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Upload PAN</label>
                                                        <label for="pan_file" class="custom-upload">
                                                            <input type="file" name="pan_file" id="pan_file">
                                                            <span class="btn btn-animated btn-upload">Upload</span>
                                                        </label>
                                                        @error('pan_file')
                                                        <span class="text-danger" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="panFilePreviewColumn" class="col-lg-4 col-12 text-center" style="display: none;">
                                                    <div id="panFilePreviewContainer">
                                                        <img id="panFilePreview" src="" alt="File Preview" class="img-fluid" style="display: none;" width="85px">
                                                        <button id="panViewFileButton" class="btn btn-primary" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">PAN Number</label>
                                                        <input type="text" name="pan_number" class="form-control {{$user_document && $user_document->pan_number?"border-red":"border-danger"}}" value="{{$user_document?$user_document->pan_number:old('pan_number')}}">
                                                        @error('pan_number')<span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row input-row align-items-center">
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Upload AADHAR</label>
                                                        <label for="adhar_file" class="custom-upload">
                                                            <input type="file" name="adhar_file" id="adhar_file">
                                                            <span class="btn btn-animated btn-upload">Upload</span>
                                                        </label>
                                                        @error('adhar_file')
                                                        <span class="text-danger" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="adharFilePreviewColumn" class="col-lg-4 col-12 text-center" style="display: none;">
                                                    <div id="adharFilePreviewContainer">
                                                        <img id="adharFilePreview" src="" alt="File Preview" class="img-fluid" style="display: none;" width="85px">
                                                        <button id="adharViewFileButton" class="btn btn-primary" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">AADHAR Number</label>
                                                        <input type="text" name="adhar_number" class="form-control {{$user_document && $user_document->adhar_number?"border-red":"border-danger"}}" value="{{$user_document?$user_document->adhar_number:old('adhar_number')}}">
                                                        @error('adhar_number')<span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row input-row align-items-center">
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Upload Trade License</label>
                                                        <label for="trade_license_file" class="custom-upload">
                                                            <input type="file" name="trade_license_file" id="trade_license_file">
                                                            <span class="btn btn-animated btn-upload">Upload</span>
                                                        </label>
                                                        @error('trade_license_file')
                                                        <span class="text-danger" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="tradeLicenseFilePreviewColumn" class="col-lg-4 col-12 text-center" style="display: none;">
                                                    <div id="tradeLicenseFilePreviewContainer">
                                                        <img id="tradeLicenseFilePreview" src="" alt="File Preview" class="img-fluid" style="display: none;" width="85px">
                                                        <button id="tradeLicenseViewFileButton" class="btn btn-primary" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Trade License Number</label>
                                                        <input type="text" name="trade_license_number" class="form-control {{$user_document && $user_document->trade_license_number?"border-red":"border-danger"}}" value="{{$user_document?$user_document->trade_license_number:old('trade_license_number')}}">
                                                        @error('trade_license_number')<span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row input-row align-items-center">
                                                <div class="col-lg-3 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Upload Cancelled Cheque</label>
                                                        <label for="cancelled_cheque_file" class="custom-upload">
                                                            <input type="file" name="cancelled_cheque_file" id="cancelled_cheque_file">
                                                            <span class="btn btn-animated btn-upload">Upload</span>
                                                        </label>
                                                        @error('cancelled_cheque_file')
                                                        <span class="text-danger" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div id="cancelledChequeFilePreviewColumn" class="col-lg-3 col-12 text-center" style="display: none;">
                                                    <div id="cancelledChequeFilePreviewContainer">
                                                        <img id="cancelledChequeFilePreview" src="" alt="File Preview" class="img-fluid" style="display: none;" width="85px">
                                                        <button id="cancelledChequeViewFileButton" class="btn btn-primary" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text" name="account_number" class="form-control {{$user_document && $user_document->account_number?"border-red":"border-danger"}}" value="{{$user_document?$user_document->account_number:old('account_number')}}">
                                                        @error('account_number')<span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">IFSC Code</label>
                                                        <input type="text" name="ifsc_code" class="form-control {{$user_document && $user_document->ifsc_code?"border-red":"border-danger"}}" value="{{$user_document?$user_document->ifsc_code:old('ifsc_code')}}">
                                                        @error('ifsc_code')<span class="text-danger" role="alert"> <strong>{{ $message }}</strong> </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            

                                            <div class="row input-row additional-doc-input-row">
                                                <div class="col-lg-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Upload Additional Documents</label>
                                                        <label for="additional_document_file" class="custom-upload">
                                                            <input type="file" name="additional_document_file[]" class="additional_document_file" id="additional_document_file" required>
                                                            <span class="btn btn-animated btn-upload">Upload</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-12 text-center additionalDocumentFilePreviewColumn" style="display: none;">
                                                    <div class="additionalDocumentFilePreviewContainer">
                                                        <img class="additionalDocumentFilePreview img-fluid" src="" alt="File Preview" style="display: none;" width="85px">
                                                        <button class="btn btn-primary additionalDocumentViewFileButton" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Additional Document</label>
                                                        <input type="text" class="form-control border-red" name="additional_documents[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-12">
                                                    <div class="form-group cta-form-group">
                                                        <a href="javascript:void(0)" class="btn-add mb-0" id="btn_add_document">
                                                            <i class="fa-solid fa-plus text-light"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="row additional-doc-output-row">
                                                <div class="offset-lg-4 col-lg-8 col-12">
                                                    <div class="form-group">
                                                        <ul>
                                                            <li>
                                                                <img src="{{asset('frontend/assets/images/aadhaar_card.jpg')}}" alt="">
                                                            </li>
                                                            <li>
                                                                <img src="{{asset('frontend/assets/images/pan_card.jpg')}}" alt="">
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="additional_document_append"></div>                                            
                                            <div class="form-submit-row">
                                                <button type="submit" class="btn btn-animated btn-submit">Submit</button>
                                            </div>
                                        </form>
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

@if($user_document && $user_document->gst_file)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileURL = '{{ asset($user_document->gst_file) }}';
            var gstFilePreviewColumn = document.getElementById('gstFilePreviewColumn');
            var gstFilePreview = document.getElementById('gstFilePreview');
            var viewFileButton = document.getElementById('viewFileButton');

            gstFilePreviewColumn.style.display = 'block';

            if ('{{ $user_document->gst_file }}'.endsWith('.pdf')) {
                gstFilePreview.style.display = 'none';
                viewFileButton.style.display = 'block';
                viewFileButton.onclick = function() {
                    window.open(fileURL, '_blank');
                };
            } else {
                gstFilePreview.style.display = 'block';
                gstFilePreview.src = fileURL;
                viewFileButton.style.display = 'none';
            }
        });
    </script>
@endif
{{-- Fetch existing file from the database --}}
@if($user_document && $user_document->pan_file)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileURL = '{{ asset($user_document->pan_file) }}';
            var panFilePreviewColumn = document.getElementById('panFilePreviewColumn');
            var panFilePreview = document.getElementById('panFilePreview');
            var viewFileButton = document.getElementById('panViewFileButton');

            panFilePreviewColumn.style.display = 'block';

            if ('{{ $user_document->pan_file }}'.endsWith('.pdf')) {
                panFilePreview.style.display = 'none';
                viewFileButton.style.display = 'block';
                viewFileButton.onclick = function() {
                    window.open(fileURL, '_blank');
                };
            } else {
                panFilePreview.style.display = 'block';
                panFilePreview.src = fileURL;
                viewFileButton.style.display = 'none';
            }
        });
    </script>
@endif

{{-- Fetch existing file from the database --}}
@if($user_document && $user_document->adhar_file)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileURL = '{{ asset($user_document->adhar_file) }}';
            var adharFilePreviewColumn = document.getElementById('adharFilePreviewColumn');
            var adharFilePreview = document.getElementById('adharFilePreview');
            var viewFileButton = document.getElementById('adharViewFileButton');

            adharFilePreviewColumn.style.display = 'block';

            if ('{{ $user_document->adhar_file }}'.endsWith('.pdf')) {
                adharFilePreview.style.display = 'none';
                viewFileButton.style.display = 'block';
                viewFileButton.onclick = function() {
                    window.open(fileURL, '_blank');
                };
            } else {
                adharFilePreview.style.display = 'block';
                adharFilePreview.src = fileURL;
                viewFileButton.style.display = 'none';
            }
        });
    </script>
@endif

{{-- Fetch existing file from the database --}}
@if($user_document && $user_document->trade_license_file)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileURL = '{{ asset($user_document->trade_license_file) }}';
            var tradeLicenseFilePreviewColumn = document.getElementById('tradeLicenseFilePreviewColumn');
            var tradeLicenseFilePreview = document.getElementById('tradeLicenseFilePreview');
            var viewFileButton = document.getElementById('tradeLicenseViewFileButton');

            tradeLicenseFilePreviewColumn.style.display = 'block';

            if ('{{ $user_document->trade_license_file }}'.endsWith('.pdf')) {
                tradeLicenseFilePreview.style.display = 'none';
                viewFileButton.style.display = 'block';
                viewFileButton.onclick = function() {
                    window.open(fileURL, '_blank');
                };
            } else {
                tradeLicenseFilePreview.style.display = 'block';
                tradeLicenseFilePreview.src = fileURL;
                viewFileButton.style.display = 'none';
            }
        });
    </script>
@endif

{{-- Fetch existing file from the database --}}
@if($user_document && $user_document->cancelled_cheque_file)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileURL = '{{ asset($user_document->cancelled_cheque_file) }}';
            var cancelledChequeFilePreviewColumn = document.getElementById('cancelledChequeFilePreviewColumn');
            var cancelledChequeFilePreview = document.getElementById('cancelledChequeFilePreview');
            var viewFileButton = document.getElementById('cancelledChequeViewFileButton');

            cancelledChequeFilePreviewColumn.style.display = 'block';

            if ('{{ $user_document->cancelled_cheque_file }}'.endsWith('.pdf')) {
                cancelledChequeFilePreview.style.display = 'none';
                viewFileButton.style.display = 'block';
                viewFileButton.onclick = function() {
                    window.open(fileURL, '_blank');
                };
            } else {
                cancelledChequeFilePreview.style.display = 'block';
                cancelledChequeFilePreview.src = fileURL;
                viewFileButton.style.display = 'none';
            }
        });
    </script>
@endif

{{-- additional --}}
{{-- @if($user_additional_document && $user_additional_document->count())
    @foreach($user_additional_document as $document)
    <div class="row input-row additional-doc-input-row">
        <div class="col-lg-4 col-12">
            <div class="form-group">
                <label class="form-label">Uploaded Document</label>
            </div>
        </div>
        <div class="col-lg-4 col-12 text-center additionalDocumentFilePreviewColumn">
            <div class="additionalDocumentFilePreviewContainer">
                @if(pathinfo($document->additional_document_file, PATHINFO_EXTENSION) == 'pdf')
                <button class="btn btn-primary additionalDocumentViewFileButton" onclick="window.open('{{ asset($document->additional_document_file) }}', '_blank')">View File</button>
                @else
                <img class="additionalDocumentFilePreview img-fluid" src="{{ asset($document->additional_document_file) }}" alt="File Preview" width="85px">
                @endif
            </div>
        </div>
    </div>
    @endforeach
@endif --}}

@section('script')
{{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

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
                    url: "{{route('user.photos_and_documents_delete')}}", // Replace 'your_delete_endpoint' with your actual delete route
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


    // additional

    $(document).ready(function() {
    var counter = 1;

    // Function to handle file preview
    function handleFilePreview(file, previewImg, viewFileButton, previewColumn) {
        if (file) {
            var fileType = file.type;

            previewColumn.show();
            previewImg.hide();
            viewFileButton.hide();

            if (fileType.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.attr('src', e.target.result).show();
                    savePreviewToLocalStorage(counter, 'image', e.target.result);
                };
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                var fileUrl = URL.createObjectURL(file);
                viewFileButton.show().attr('onclick', `window.open('${fileUrl}', '_blank')`);
                savePreviewToLocalStorage(counter, 'pdf', fileUrl);
            } else {
                alert('Please upload an image or PDF file.');
            }
        } else {
            previewColumn.hide();
        }
    }

    // Function to save preview information to local storage
    function savePreviewToLocalStorage(index, type, data) {
        var previews = JSON.parse(localStorage.getItem('documentPreviews')) || {};
        previews[index] = { type: type, data: data };
        localStorage.setItem('documentPreviews', JSON.stringify(previews));
    }

    // Function to load previews from local storage
    function loadPreviewsFromLocalStorage() {
        var previews = JSON.parse(localStorage.getItem('documentPreviews')) || {};
        $.each(previews, function(index, preview) {
            var template = `
                <div class="row input-row additional-doc-input-row">
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <label class="form-label">Upload Additional Documents</label>
                            <label for="additional_document_file${index}" class="custom-upload">
                                <input type="file" name="additional_document_file[]" class="additional_document_file" id="additional_document_file${index}" required>
                                <span class="btn btn-animated btn-upload">Upload</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 text-center additionalDocumentFilePreviewColumn" id="additionalDocumentFilePreviewColumn${index}">
                        <div class="additionalDocumentFilePreviewContainer" id="additionalDocumentFilePreviewContainer${index}">
                            <img class="additionalDocumentFilePreview img-fluid" id="additionalDocumentFilePreview${index}" src="" alt="File Preview" style="display: none;" width="85px">
                            <button class="btn btn-primary additionalDocumentViewFileButton" id="additionalDocumentViewFileButton${index}" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label class="form-label">Additional Document</label>
                            <input type="text" class="form-control border-red" name="additional_documents[]" required>
                        </div>
                    </div>
                    <div class="col-lg-1 col-12">
                        <div class="form-group cta-form-group">
                            <button type="button" class="btn-remove">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 6H5H21" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M10 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M14 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>`;
            $('#additional_document_append').append(template);

            var previewImg = $('#additionalDocumentFilePreview' + index);
            var viewFileButton = $('#additionalDocumentViewFileButton' + index);
            var previewColumn = $('#additionalDocumentFilePreviewColumn' + index);

            if (preview.type === 'image') {
                previewImg.attr('src', preview.data).show();
            } else if (preview.type === 'pdf') {
                viewFileButton.show().attr('onclick', `window.open('${preview.data}', '_blank')`);
            }
            previewColumn.show();
        });
    }
  
    // Click event for the add button
    $('#btn_add_document').click(function() {
        var template = `
            <div class="row input-row additional-doc-input-row">
                <div class="col-lg-4 col-12">
                    <div class="form-group">
                        <label class="form-label">Upload Additional Documents</label>
                        <label for="additional_document_file${counter}" class="custom-upload">
                            <input type="file" name="additional_document_file[]" class="additional_document_file" id="additional_document_file${counter}" required>
                            <span class="btn btn-animated btn-upload">Upload</span>
                        </label>
                    </div>
                </div>
                <div class="col-lg-4 col-12 text-center additionalDocumentFilePreviewColumn" id="additionalDocumentFilePreviewColumn${counter}" style="display: none;">
                    <div class="additionalDocumentFilePreviewContainer" id="additionalDocumentFilePreviewContainer${counter}">
                        <img class="additionalDocumentFilePreview img-fluid" id="additionalDocumentFilePreview${counter}" src="" alt="File Preview" style="display: none;" width="85px">
                        <button class="btn btn-primary additionalDocumentViewFileButton" id="additionalDocumentViewFileButton${counter}" style="display: none;" onclick="window.open('', '_blank')">View File</button>
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label class="form-label">Additional Document</label>
                        <input type="text" class="form-control border-red" name="additional_documents[]" required>
                    </div>
                </div>
                <div class="col-lg-1 col-12">
                    <div class="form-group cta-form-group">
                        <button type="button" class="btn-remove">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6H5H21" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M10 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M14 11V17" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>`;
        $('#additional_document_append').append(template);
        counter++;
    });

    // Click event for the delete button
    $(document).on('click', '.btn-remove', function() {
        $(this).closest('.additional-doc-input-row').remove();
    });

    // File input change event for previewing
    $(document).on('change', '.additional_document_file', function() {
        var file = this.files[0];
        var previewImg = $(this).closest('.additional-doc-input-row').find('.additionalDocumentFilePreview');
        var viewFileButton = $(this).closest('.additional-doc-input-row').find('.additionalDocumentViewFileButton');
        var previewColumn = $(this).closest('.additional-doc-input-row').find('.additionalDocumentFilePreviewColumn');
        var index = $(this).attr('id').replace('additional_document_file', '');
        handleFilePreview(file, previewImg, viewFileButton, previewColumn);
    });

    // Load previews from local storage when the page loads
    loadPreviewsFromLocalStorage();
});





// gst file
document.getElementById('gst_file').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var previewImg = document.getElementById('gstFilePreview');
    var viewFileButton = document.getElementById('viewFileButton');
    var previewColumn = document.getElementById('gstFilePreviewColumn');
    var fileUrl;

    if (file) {
        var fileType = file.type;

        // Show the preview column
        previewColumn.style.display = 'block';

        // Clear previous previews
        previewImg.style.display = 'none';
        viewFileButton.style.display = 'none';

        if (fileType.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';  // Display the image
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        } else if (fileType === 'application/pdf') {
            fileUrl = URL.createObjectURL(file);
            viewFileButton.style.display = 'block';  // Display the View File button
            viewFileButton.onclick = function() {
                window.open(fileUrl, '_blank');  // Open the PDF in a new tab
            };
        } else {
            alert('Please upload an image or PDF file.');
        }
    } else {
        // Hide the preview column if no file is selected
        previewColumn.style.display = 'none';
    }
});

// pan card
document.getElementById('pan_file').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var previewImg = document.getElementById('panFilePreview');
        var viewFileButton = document.getElementById('panViewFileButton');
        var previewColumn = document.getElementById('panFilePreviewColumn');
        var fileUrl;

        if (file) {
            var fileType = file.type;

            // Show the preview column
            previewColumn.style.display = 'block';

            // Clear previous previews
            previewImg.style.display = 'none';
            viewFileButton.style.display = 'none';

            if (fileType.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';  // Display the image
                };
                reader.readAsDataURL(file); // Read the file as a data URL
            } else if (fileType === 'application/pdf') {
                fileUrl = URL.createObjectURL(file);
                viewFileButton.style.display = 'block';  // Display the View File button
                viewFileButton.onclick = function() {
                    window.open(fileUrl, '_blank');  // Open the PDF in a new tab
                };
            } else {
                alert('Please upload an image or PDF file.');
            }
        } else {
            // Hide the preview column if no file is selected
            previewColumn.style.display = 'none';
        }
    });

// aadhar file
document.getElementById('adhar_file').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var previewImg = document.getElementById('adharFilePreview');
    var viewFileButton = document.getElementById('adharViewFileButton');
    var previewColumn = document.getElementById('adharFilePreviewColumn');
    var fileUrl;

    if (file) {
        var fileType = file.type;

        // Show the preview column
        previewColumn.style.display = 'block';

        // Clear previous previews
        previewImg.style.display = 'none';
        viewFileButton.style.display = 'none';

        if (fileType.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';  // Display the image
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        } else if (fileType === 'application/pdf') {
            fileUrl = URL.createObjectURL(file);
            viewFileButton.style.display = 'block';  // Display the View File button
            viewFileButton.onclick = function() {
                window.open(fileUrl, '_blank');  // Open the PDF in a new tab
            };
        } else {
            alert('Please upload an image or PDF file.');
        }
    } else {
        // Hide the preview column if no file is selected
        previewColumn.style.display = 'none';
    }
});

// trade license
document.getElementById('trade_license_file').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var previewImg = document.getElementById('tradeLicenseFilePreview');
    var viewFileButton = document.getElementById('tradeLicenseViewFileButton');
    var previewColumn = document.getElementById('tradeLicenseFilePreviewColumn');
    var fileUrl;

    if (file) {
        var fileType = file.type;

        // Show the preview column
        previewColumn.style.display = 'block';

        // Clear previous previews
        previewImg.style.display = 'none';
        viewFileButton.style.display = 'none';

        if (fileType.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';  // Display the image
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        } else if (fileType === 'application/pdf') {
            fileUrl = URL.createObjectURL(file);
            viewFileButton.style.display = 'block';  // Display the View File button
            viewFileButton.onclick = function() {
                window.open(fileUrl, '_blank');  // Open the PDF in a new tab
            };
        } else {
            alert('Please upload an image or PDF file.');
        }
    } else {
        // Hide the preview column if no file is selected
        previewColumn.style.display = 'none';
    }
});   

// cancelled_cheque_file
document.getElementById('cancelled_cheque_file').addEventListener('change', function(event) {
    var file = event.target.files[0];
    var previewImg = document.getElementById('cancelledChequeFilePreview');
    var viewFileButton = document.getElementById('cancelledChequeViewFileButton');
    var previewColumn = document.getElementById('cancelledChequeFilePreviewColumn');
    var fileUrl;

    if (file) {
        var fileType = file.type;

        // Show the preview column
        previewColumn.style.display = 'block';

        // Clear previous previews
        previewImg.style.display = 'none';
        viewFileButton.style.display = 'none';

        if (fileType.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';  // Display the image
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        } else if (fileType === 'application/pdf') {
            fileUrl = URL.createObjectURL(file);
            viewFileButton.style.display = 'block';  // Display the View File button
            viewFileButton.onclick = function() {
                window.open(fileUrl, '_blank');  // Open the PDF in a new tab
            };
        } else {
            alert('Please upload an image or PDF file.');
        }
    } else {
        // Hide the preview column if no file is selected
        previewColumn.style.display = 'none';
    }
});

</script>
@endsection