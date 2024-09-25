@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Edit Assistant Supervisor</h2>
            </div>
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
    <form action="{{ route('assistantSupervisors.update',$assistant_supervisor->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name', $assistant_supervisor->name) }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Email:</strong>
                    <input type="email" name="email" value="{{ old('email', $assistant_supervisor->email) }}" class="form-control" placeholder="abc@gmail.com">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Password:</strong>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Confirm Password:</strong>
                    <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Supervisor:</strong>
                    <select name="supervisor_id" class="form-control">
                        <option></option>
                        @foreach($supervisors as $supervisor)
                        @if($supervisor->hasRole("Supervisor"))
                        @if(count($assistant_supervisor->AssistantSupervisorToSupervisor))
                        @foreach($assistant_supervisor->AssistantSupervisorToSupervisor as $assistantSupervisorToSupervisor)
                        @if($supervisor->id == $assistantSupervisorToSupervisor->supervisor_id)
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }} selected>{{ $supervisor->name }}</option>
                        @else
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                        @endif
                        @endforeach
                        @else
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                        @endif
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
@endsection