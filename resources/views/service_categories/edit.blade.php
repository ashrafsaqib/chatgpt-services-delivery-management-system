@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Service Category</h2>
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
    <form action="{{ route('serviceCategories.update',$service_category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Title:</strong>
                    <input type="text" name="title" value="{{ old('title',$service_category->title) }}" class="form-control" placeholder="Title">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="image">Upload Image</strong>
                    <input type="file" name="image" id="image" class="form-control-file">
                    <br>
                    <img id="preview" src="/service-category-images/{{$service_category->image}}" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="icon">Upload Icon</strong>
                    <input type="file" name="icon" id="icon" class="form-control-file">
                    <br>
                    <img id="icon-preview" src="/service-category-icons/{{$service_category->icon}}" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $service_category->status ) == '1' ? 'selected' : '' }}>Enable</option>
                        <option value="0" {{ old('status', $service_category->status ) == '0' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Description:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{$service_category->description}}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Type:</strong>
                    <select name="type" class="form-control">
                        <option value="Male" {{ old('type', $service_category->type) === 'Male' ? 'selected' : '' }} >Male</option>
                        <option value="Female" {{ old('type', $service_category->type) === 'Female' ? 'selected' : '' }} >Female</option>
                        <option value="Both" {{ old('type', $service_category->type) === 'Both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Parent Category:</strong>
                    <select name="parent_id" class="form-control">
                        <option></option>
                        @foreach($categories as $category)
                        @if($category->id == $service_category->parent_id)
                        <option value="{{$category->id}}" selected>{{$category->title}}</option>
                        @else
                        <option value="{{$category->id}}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>{{$category->title}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
<script>
    document.getElementById('icon').addEventListener('change', function(e) {
        var preview = document.getElementById('icon-preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection
