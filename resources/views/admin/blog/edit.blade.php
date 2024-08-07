@extends('admin.app')
{{-- @section('title') {{ $pageTitle }} @endsection --}}
@section('content')
<div class="inner-content">
    <div class="report-table-box">
        <div class="heading-row mb-2">
            <h3>Update Blog</h3>
            <a href="{{route('admin.blog.index')}}" class="btn btn-danger btn-sm">
                <iconify-icon icon="icon-park-twotone:back"></iconify-icon>
                Back 
            </a>
        </div>
        <form action="{{route('admin.blog.update')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12">
                <div class="form-wrap mb-3">
                        <label for="">Upload Blog</label>
                        <input type="file" class="form-control" name="image" id="image" value="{{old('image')}}">
                        @error('image')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-wrap mb-3">
                        <label for="">Title</label>
                        <input type="text" class="form-control" name="title" id="title" value="{{ $data->title }}">
                        @error('title')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-wrap mb-3">
                        <label for="">Short Description</label>
                        <textarea type="text" class="form-control ckeditor" name="short_description" id="short_description">{{ $data->short_desc }}</textarea>
                        @error('short_description')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-wrap mb-3">
                        <label for="">Long Description</label>
                        <textarea type="text" class="form-control ckeditor" name="long_description" id="long_description">{{ $data->long_desc }}</textarea>
                        @error('long_description')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-wrap mb-3">
                                <label for="">Page Title</label>
                                <input type="text" class="form-control" name="page_title" id="page_title" value="{{ $data->page_title }}">
                                @error('page_title')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-wrap mb-3">
                                <label for="">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ $data->meta_title }}">
                                @error('meta_title')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-wrap mb-3">
                                <label for="">Meta Description</label>
                                <textarea type="text" class="form-control" name="meta_description" id="meta_description">{{ $data->meta_desc }}</textarea>
                                @error('meta_description')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-wrap mb-3">
                                <label for="">Meta Keyword</label>
                                <input type="text" class="form-control" name="meta_keyword" id="meta_keyword" value="{{ $data->meta_keyword }}">
                                @error('meta_keyword')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                   <input type="hidden" name="id" value="{{$data->id}}">
                   <input type="hidden" name="old_blog_img" value="{{$data->image}}">
                    <div class="form-wrap">
                        <input type="submit" value="Save" class="btn btn-save ms-auto">
                    </div>
                </div>
            </div>
          </form>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript" src="{{ asset('frontend/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/ckeditor/adapters/jquery.js') }}"></script>
@endpush