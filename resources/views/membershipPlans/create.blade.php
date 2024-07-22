@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Add New Membership Plan</h2>
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
        <form action="{{ route('membershipPlans.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Plan Name:</strong>
                        <input type="text" name="plan_name" class="form-control" value="{{ old('plan_name') }}"
                            placeholder="Plan Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Membership Fee:</strong>
                        <input type="number" name="membership_fee" class="form-control" value="{{ old('membership_fee') }}"
                            placeholder="Membership Fee">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expire after days:</strong>
                        <input type="number" name="expire" class="form-control" value="{{ old('expire') }}"
                            placeholder="Enter days like 20">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Type:</strong>
                        <select name="type" class="form-control">
                            <option></option>
                            <option value="Affiliate" @if (old('type') == 'Affiliate') selected @endif>Affiliate</option>
                            <option value="Freelancer" @if (old('type') == 'Freelancer') selected @endif>Freelancer</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option></option>
                            <option value="1" @if (old('status') == '1') selected @endif>Enable</option>
                            <option value="0" @if (old('status') == '0') selected @endif>Disable</option>
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
