@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Review</h2>
    </div>
</div>
@if ($errors->any())
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Your Name:</strong>
                <input type="text" name="user_name" value="{{ old('user_name') }}" class="form-control">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Service:</strong>
                <select name="service_id" class="form-control">
                    <option></option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Review:</strong>
                <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{old('content')}}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong for="image">Upload Image</strong>
                <input type="file" name="image" id="image" class="form-control-file ">
                <br>
                <img id="preview" src="/review-images/" height="130px">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><label for="rating">Rating</label><br>
                @for($i = 1; $i <= 5; $i++) <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
            </div>
            @endfor
        </div>
    </div>

    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
    </div>
</form>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection