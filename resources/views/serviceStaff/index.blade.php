@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="float-left">
            <h2>Service Staff</h2>
        </div>
        <div class="float-right">
            @can('service-staff-create')
            <a class="btn btn-success float-end" href="{{ route('serviceStaff.create') }}"><i class="fa fa-plus"></i></a>
            @endcan
        </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <hr>
    @php
        $i = 0;
    @endphp
    <div class="row">
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($serviceStaff))
                @foreach ($serviceStaff as $staff)
                @if($staff->getRoleNames() == '["Staff"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>@if($staff->staff->status == 1) Enabled @else Disabled @endif</td>
                    <td>
                        <form action="{{ route('serviceStaff.destroy',$staff->id) }}" method="POST">
                            <a class="btn btn-warning" href="{{ route('serviceStaff.show',$staff->id) }}"><i class="fa fa-eye"></i></a>
                            @can('service-staff-edit')
                            <a class="btn btn-primary" href="{{ route('serviceStaff.edit',$staff->id) }}"><i class="fa fa-edit"></i></a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('service-staff-delete')
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                            @endcan
                            <a class="btn btn-primary" href="{{ route('staffHolidays.create',['staff' => $staff->id]) }}" title="Add Holiday"><i class="fas fa-calendar"></i></a>
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Staff.</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="{{ route('serviceStaff.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{$filter_name}}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection