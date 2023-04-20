@extends('site.layout.app')
@section('content')
<div class="content">
<div class="row">
    <div class="col-lg-12 py-5 text-center">
        <h2>Assigned Service</h2>
    </div>
</div>
<div class="container">
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
    <div class="text-right">
        <a class="btn btn-primary float-end no-print" onclick="printDiv()"><i class="fa fa-print"></i></a>
    </div><br>

    @if(count($booked_services) != 0)
    <table class="table table-bordered album bg-light">
        <tr>
            <th>No</th>
            <th>Service</th>
            <th>Price</th>
            <th>Status</th>
            <th>date</th>
            <th>Time</th>
            <th class="no-print">Action</th>
        </tr>
        @foreach ($booked_services as $booked_service)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $booked_service->service->name }}</td>
            <td>{{ $booked_service->service->price }}</td>
            <td>{{ $booked_service->status }}</td>
            <td>{{ $booked_service->date }}</td>
            <td>{{ $booked_service->time }}</td>
            <td class="no-print">
                <a class="btn btn-primary" href="{{ route('booking.edit',$booked_service->id) }}">Edit</a>
            </td>
        </tr>
        @endforeach
        
    </table>
    @else
    <div class="text-center">
        <h4>There is no assigned services.</h4>
    </div>
    @endif
  </div>
</div>
</div>
<script>
    function printDiv() {
        window.print();
    }
    </script>
@endsection