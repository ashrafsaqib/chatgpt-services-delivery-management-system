@extends('layouts.app')
<style>
.fc-day-grid-event {
    padding: 13px 0px !important;
}
.alert {
    padding: 0.5rem 0.5rem !important;
}
</style>
@section('content')
    <div class="container" style="max-width: 700px">
        <h3 class="h3 text-center border-bottom pb-3">Holiday Calendar</h3>
        @if(count(config('app.general_holiday')))
        <div>
            <h3>General Holidays</h3>
            <ul>
                @foreach(config('app.general_holiday') as $holiday)
                <li>{{ $holiday }}</li>
                
                @endforeach
            </ul>
        </div>
        @endif
        <div class="alert alert-success" style="display: none">
            <span></span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="alert alert-info">
            <b>Note: </b><span>Click on date to add holiday and click on holiday date to delete.</span>
        </div>
        <div id='full_calendar_events'></div>
    </div>
    
    <script>
        $(document).ready(function () {
            var SITEURL = "{{ url('/') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var calendar = $('#full_calendar_events').fullCalendar({
                editable: false,
                editable: false,
                events: SITEURL + "/holidays",
                displayEventTime: true,
                selectable: true,
                selectHelper: true,
                select: function (date) {
                        var date = $.fullCalendar.formatDate(date, "Y-MM-DD");
                        $.ajax({
                            cache: false, 
                            url: SITEURL + "/holidays/crud-ajax",
                            data: {
                                date: date,
                                type: 'create'
                            },
                            type: "POST",
                            success: function (data) {
                                displayMessage("Holiday created.");
                                calendar.fullCalendar('renderEvent', {
                                    id: data.id,
                                    date: date
                                }, true);
                                calendar.fullCalendar('unselect');
                            }
                        });
                },
                eventClick: function (event) {
                    var eventDelete = confirm("Are you sure to Delete?");
                    if (eventDelete) {
                        $.ajax({
                            cache: false, 
                            type: "POST",
                            url: SITEURL + '/holidays/crud-ajax',
                            data: {
                                id: event.id,
                                type: 'delete'
                            },
                            success: function (response) {
                                calendar.fullCalendar('removeEvents', event.id);
                                displayMessage("Holiday removed");
                            }
                        });
                    }
                }
            });
        });
        function displayMessage(message) {
            var alertBox = $('.alert.alert-success');
            alertBox.find('span').text(message);
            alertBox.show();
            setTimeout(function () {
                alertBox.hide();
            }, 3000);
        }
    </script>
@endsection
