@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Time Slots</h2>
        </div>
        <div class="col-md-6">
            <a class="btn btn-success  float-end" href="{{ route('timeSlots.create') }}"> Create New Service Category</a>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <hr>
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Time Start</th>
            <th>Time End</th>
            <th>Active</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($time_slots))
        @foreach ($time_slots as $time_slot)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $time_slot->time_start }}</td>
            <td>{{ $time_slot->time_end }}</td>
            <td>{{ $time_slot->active }}</td>
            <td>
                <form action="{{ route('timeSlots.destroy',$time_slot->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('timeSlots.show',$time_slot->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('timeSlots.edit',$time_slot->id) }}">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="5" class="text-center">There is no time slots.</td>
        </tr>
        @endif
    </table>
    {!! $time_slots->links() !!}
@endsection