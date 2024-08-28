@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Driver ({{ $total_driver }})</h2>
                </div>
                <div class="float-end">
                    @can('driver-create')
                        <a class="btn btn-success" href="{{ route('drivers.create') }}"> Create New Driver</a>
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
        <div class="row">
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('drivers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('drivers.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Commission</th>
                        <th>Affiliate</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($drivers))
                        @foreach ($drivers as $driver)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $driver->name }}</td>
                                <td>{{ $driver->email }}</td>
                                <td>{{ $driver->driver->commission ?? "" }}</td>
                                <td>{{ $driver->driver->affiliate ? $driver->driver->affiliate->name : "" }}</td>

                                <td>
                                    <form id="deleteForm{{ $driver->id }}"
                                        action="{{ route('drivers.destroy', $driver->id) }}" method="POST">
                                        <a class="btn btn-info" href="{{ route('drivers.show', $driver->id) }}"><i class="fa fa-eye"></i></a>
                                        @can('driver-edit')
                                            <a class="btn btn-primary" href="{{ route('drivers.edit', $driver->id) }}"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('driver-delete')
                                            <button type="button" onclick="confirmDelete('{{ $driver->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no driver</td>
                        </tr>
                    @endif
                </table>
                {!! $drivers->links() !!}
            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('drivers.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter_name }}" class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });
        });
    </script>
@endsection
