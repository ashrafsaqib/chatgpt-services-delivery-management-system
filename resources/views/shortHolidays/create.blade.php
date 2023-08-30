@extends('layouts.app')
@section('content')
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Short Holiday</h2>
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
    <form action="{{ route('shortHolidays.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
         <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date:</strong>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time Start:</strong>
                    <input type="time" name="time_start" value="{{ date('Y-m-d') }}" class="form-control" placeholder="TIme Start">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time End:</strong>
                    <input type="time" name="time_end" value="{{ date('Y-m-d') }}" class="form-control" placeholder="TIme End">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Staff:</strong>
                    <select name="staff_id" class="form-control">
                    @foreach ($staffs as $staff)
                        @if($staff->getRoleNames() == '["Staff"]')
                        @if($staff->id == $staff_id)
                            <option value="{{ $staff->id }}" selected>{{ $staff->name }}</option>
                        @else
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endif
                        @endif
                    @endforeach
                    </select>
                </div>
            </div>
            <!-- <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Staffs:</strong>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search Staff By Name And Email">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        @foreach ($staffs as $staff)
                        @if($staff->getRoleNames() == '["Staff"]')
                        <tr>
                            <td>
                                @if($staff->id == $staff_id)
                                    <input type="checkbox" name="ids[{{ ++$i }}]" checked value="{{ $staff->id }}">
                                @else
                                    <input type="checkbox" name="ids[{{ ++$i }}]" value="{{ $staff->id }}">
                                @endif
                            </td>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </table>
                </div>
            </div> -->
            <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <script>
$(document).ready(function(){
    $("#search").keyup(function(){
        var value = $(this).val().toLowerCase();
        
        $("table tr").hide();

        $("table tr").each(function() {

            $row = $(this);

            var name = $row.find("td:first").next().text().toLowerCase();

            var email = $row.find("td:last").text().toLowerCase();

            if (name.indexOf(value) != -1) {
                $(this).show();
            }else if(email.indexOf(value) != -1) {
                $(this).show();
            }
        });
    });
});
</script>
@endsection