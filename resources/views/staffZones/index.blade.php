@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Staff Zone ({{ $total_staffZone }})</h2>
            </div>
            <div class="col-md-6">
                @can('staff-zone-create')
                    <a class="btn btn-success  float-end" href="{{ route('staffZones.create') }}"> Create New Staff Zone</a>
                @endcan
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
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffZones.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffZones.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                            @if (request('sort') === 'description')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffZones.index', array_merge(request()->query(), ['sort' => 'transport_charges', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Transport
                                Charges</a>
                            @if (request('sort') === 'transport_charges')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Currency</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($staffZones))
                        @foreach ($staffZones as $staffZone)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $staffZone->name }}</td>
                                <td>{{ $staffZone->description }}</td>
                                <td>{{ $staffZone->transport_charges }}</td>
                                <td>{{ $staffZone->currency->name ?? '' }}</td>
                                <td>
                                    <form id="deleteForm{{ $staffZone->id }}"
                                        action="{{ route('staffZones.destroy', $staffZone->id) }}" method="POST">
                                        <a class="btn btn-info"
                                            href="{{ route('staffZones.show', $staffZone->id) }}"><i class="fa fa-eye"></i></a>
                                        @can('staff-zone-edit')
                                            <a class="btn btn-primary"
                                                href="{{ route('staffZones.edit', $staffZone->id) }}"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-zone-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staffZone->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no staff zone.</td>
                        </tr>
                    @endif
                </table>
                {!! $staffZones->links() !!}
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
