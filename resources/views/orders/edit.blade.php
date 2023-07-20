@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Edit Order</h2>
    </div>
</div>
<div class="container">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div>
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
        <form action="{{ route('orders.update',$order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Date:</strong>
                        <input type="date" name="date" id="date" value="{{ $order->date }}" class="form-control" placeholder="Date">
                    </div>
                </div>
                <div class="col-md-12">
                    <strong>Time Slots</strong>
                    <input type="hidden" name="city" value="{{ $order->city }}">
                    <input type="hidden" name="area" value="{{ $order->area }}">
                    <div class="list-group" id="time-slots-container">
                    @include('site.checkOut.timeSlots')
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group" id="detail-container">
                        <strong>Selected Time Slot:</strong><span id="selected-time-slot">{{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }}</span><br>
                        <strong>Selected Staff:</strong><span id="selected-staff">{{ $order->staff->user->name }}</span>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            @foreach ($statuses as $status)
                            @if($status == $order->status)
                            <option value="{{ $status }}" selected>{{ $status }}</option>
                            @else
                            <option value="{{ $status }}">{{ $status }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 text-right no-print">
                    @can('order-edit')
                    <button type="submit" class="btn btn-primary">Update</button>
                    @endcan
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}"></script>
@endsection