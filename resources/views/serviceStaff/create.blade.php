@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Service Staff</h2>
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
    <form action="{{ route('serviceStaff.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            @if($socialLinks)
            <li class="nav-item">
                <a class="nav-link" id="social-links-tab" data-toggle="tab" href="#social-links" role="tab" aria-controls="social-links" aria-selected="false">Social Links</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab" aria-controls="gallery" aria-selected="false">Gallery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="category-services-tab" data-toggle="tab" href="#category-services" role="tab" aria-controls="category-services" aria-selected="false">Categories & Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab" aria-controls="document" aria-selected="false">Document</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name:</strong>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Sub Title / Designation</strong>
                            <input type="text" name="sub_title" class="form-control" value="{{ old('sub_title') }}" placeholder="Sub Title">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Email:</strong>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Phone Number:</strong>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input type="tel" id="number" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option value="1"  {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>About:</strong>
                            <textarea name="about" id="summernote" class="form-control">{{ old('about')}}</textarea>
                            <script>
                                (function($) {
                                    $('#summernote').summernote({
                                        tabsize: 2,
                                        height: 250,
                                        callbacks: {
                                            onImageUpload: function(files) {
                                                uploadImage(files[0]);
                                            }
                                        }
                                    });
    
                                    function uploadImage(file) {
                                        let data = new FormData();
                                        data.append("file", file);
                                        data.append("_token", "{{ csrf_token() }}");
    
                                        $.ajax({
                                            url: "{{ route('summerNote.upload') }}",
                                            method: "POST",
                                            data: data,
                                            processData: false,
                                            contentType: false,
                                            success: function(response) {
                                                $('#summernote').summernote('insertImage', response.url);
                                            },
                                            error: function(response) {
                                                console.error(response);
                                            }
                                        });
                                    }
                                })(jQuery);
                            </script>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong for="image">Upload Image</strong>
                            <input type="file" name="image" class="class="form-control" image-input" accept="image/*">
                            <img class="image-preview" height="130px">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Password:</strong>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Confirm Password:</strong>
                            <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Assign Drivers for Each Day:</strong>
                            <table id="weekly-drivers" class="table table-bordered supervisor-table" style="width: 100%; margin-bottom: 20px;">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Driver</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                        <tr id="{{ $day }}-first-row" data-day="{{ $day }}">
                                            <td rowspan="1" class="day-name">{{ $day }}</td>
                                            <td>
                                                <select name="drivers[{{ $day }}][0][driver_id]" class="form-control" required>
                                                    @foreach ($users as $driver)
                                                        @if ($driver->hasRole("Driver"))
                                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" name="drivers[{{ $day }}][0][start_time]" class="form-control" required></td>
                                            <td><input type="time" name="drivers[{{ $day }}][0][end_time]" class="form-control" required></td>
                                            <td>
                                                <button type="button" class="btn btn-primary" onclick="addDriverRow('{{ $day }}')">Add</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <strong>Supervisor:</strong>
                            <input type="text" name="search-supervisor" id="search-supervisor" class="form-control" placeholder="Search Supervisor By Name And Email">
                            <table class="table table-striped table-bordered supervisor-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                @foreach ($users as $user)
                                @if($user->hasRole("Supervisor"))
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $user->id }}" {{ in_array($user->id, old('ids', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Commission:</strong>
                            <input type="number" name="commission" class="form-control" value="{{ old('commission') }}" placeholder="Commission In %">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Additional Charges:</strong>
                            <input type="number" name="charges" class="form-control" value="{{ old('charges') }}" placeholder="Additional Charges">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Commission Salary:</strong>
                            <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary') }}" placeholder="Commission Salary">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Minmum Order Value:</strong>
                            <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value') }}" placeholder="Minmum Order Value">
                        </div>
                    </div>
                </div>
            </div>
            @if($socialLinks)
            <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Instagram:</strong>
                            <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="{{ old('instagram') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Snapchat:</strong>
                            <input type="text" name="snapchat" class="form-control" placeholder="Snapchat" value="{{ old('snapchat') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Facebook:</strong>
                            <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="{{ old('facebook') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Youtube:</strong>
                            <input type="text" name="youtube" class="form-control" placeholder="Youtube" value="{{ old('youtube') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Tiktok:</strong>
                            <input type="text" name="tiktok" class="form-control" placeholder="Tiktok" value="{{ old('tiktok') }}">
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                <div class="row">
                    <div class="col-md-12">
                        <strong>Youtube Video:</strong>

                        <div class="form-group" id="video-div">
                        </div>
                        <button id="addVideoBtn" type="button" class="btn btn-primary float-right">Add Youtube Video</button>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Images:</strong>
                            <table id="imageTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Images</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <button id="addImageBtn" type="button" class="btn btn-primary float-right">Add Image</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="category-services" role="tabpanel" aria-labelledby="category-services-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Category:</strong>
                            <input type="text" name="search-category" id="search-category" class="form-control" placeholder="Search category By Name, Price And Duration">
                            <table class="table table-striped table-bordered category-table">
                                <tr>
                                    <th></th>
                                    <th>Title</th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="category-checkbox"  name="category" value="all">
                                    </td>
                                    <td>All</td>
                                </tr>
                                @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="category-checkbox" name="category_ids[]" value="{{ $category->id }}" {{ (is_array(old('category_ids')) && in_array($category->id, old('category_ids'))) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $category->title }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Services:</strong>
                            <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name, Price And Duration">
                            <table class="table table-striped table-bordered services-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="service-checkbox" name="service" value="all">
                                    </td>
                                    <td>All</td>
                                </tr>
                                @foreach ($services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="service-checkbox" name="service_ids[]" {{ (is_array(old('service_ids')) && in_array($service->id, old('service_ids'))) ? 'checked' : '' }} value="{{ $service->id }}" data-category="{{ $service->category_id }}">
                                    </td>
                                    <td>{{ $service->name }}</td>

                                    <td>{{ isset($service->discount) ? 
                                    $service->discount : $service->price }}</td>
                                    <td>{{ $service->duration }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                <div class="row">
                    @foreach($documents as $field => $label)
                        <div class="col-md-12">
                            <div class="form-group">
                                @if(in_array($field, ['address_proof', 'id_card', 'passport', 'driving_license', 'education']))
                                    <span style="color: red;">*</span>
                                @endif
                                <strong>{{ $label }}:</strong>
                                <input type="file" name="{{ $field }}" class="form-control">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-block btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>
<script>
    let rowCounts = {
        Monday: 1,
        Tuesday: 1,
        Wednesday: 1,
        Thursday: 1,
        Friday: 1,
        Saturday: 1,
        Sunday: 1
    };
    
    function addDriverRow(day) {
        const tableBody = document.querySelector(`#weekly-drivers tbody`);
        rowCounts[day]++;
    
        const newRow = document.createElement('tr');
        newRow.classList.add('driver-row');
        newRow.setAttribute('data-day', day);
    
        newRow.innerHTML = `
            <td>
                <select name="drivers[${day}][${rowCounts[day]}][driver_id]" class="form-control" required>
                    @foreach ($users as $driver)
                        @if ($driver->hasRole("Driver"))
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td><input type="time" name="drivers[${day}][${rowCounts[day]}][start_time]" class="form-control" required></td>
            <td><input type="time" name="drivers[${day}][${rowCounts[day]}][end_time]" class="form-control" required></td>
            <td>
                <button type="button" class="btn btn-danger" onclick="removeDriverRow(this)">Remove</button>
            </td>
        `;
    
        // Append the new row after the last row of the day
        const dayRows = Array.from(tableBody.querySelectorAll(`tr[data-day="${day}"]`));
        const lastDayRow = dayRows[dayRows.length - 1];
        lastDayRow.insertAdjacentElement('afterend', newRow);
    
        // Adjust the rowspan for the day cell
        const dayCell = document.querySelector(`#${day}-first-row .day-name`);
        dayCell.setAttribute('rowspan', rowCounts[day]);
    }
    
    function removeDriverRow(button) {
        const row = button.closest('tr');
        const day = row.getAttribute('data-day');
        row.remove();
    
        // Decrease the rowspan count for the day cell
        rowCounts[day]--;
        const dayCell = document.querySelector(`#${day}-first-row .day-name`);
        dayCell.setAttribute('rowspan', rowCounts[day]);
    }
    $("#search-services").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".services-table tr").hide();

        $(".services-table tr").each(function() {
            let $row = $(this);

            let name = $row.find("td:nth-child(2)").text().toLowerCase();

            if (name.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });
    $("#search-category").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".category-table tr").hide();

        $(".category-table tr").each(function() {
            let $row = $(this);

            let title = $row.find("td:nth-child(2)").text().toLowerCase();

            if (title.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });

    $('.category-checkbox').click(function() {
        var categoryId = $(this).val();

        if (categoryId === 'all') {
            var allCheckboxState = $(this).prop('checked');
            $('.category-checkbox').prop('checked', allCheckboxState);
        }
    });

    $('.service-checkbox').click(function() {
        var serviceId = $(this).val();

        if (serviceId === 'all') {
            var allCheckboxState = $(this).prop('checked');
            $('.service-checkbox').prop('checked', allCheckboxState);
        }
    });
</script>
<script>
    $(document).ready(function() {
        $("#addImageBtn").click(function() {
            // Append a new row to the table
            $("#imageTable tbody").append(`
                <tr>
                    <td>
                        <input type="file" name="gallery_images[]" class="class="form-control" image-input" accept="image/*">
                        <img class="image-preview" height="130px">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-image">Remove</button>
                    </td>
                </tr>
            `);
        });

        $("#addVideoBtn").click(function() {
            // Append a new row to the table
            $("#video-div").append(`
                <div class="form-group">
                    <input type="text" name="youtube_video[]" class="form-control" placeholder="Youtube Video">
                </div>
            `);
        });



        $(document).on("click", ".remove-image", function() {
            $(this).closest("tr").html('');
        });

        $(document).on("change", ".image-input", function(e) {
            var preview = $(this).siblings('.image-preview')[0];
            preview.src = URL.createObjectURL(e.target.files[0]);
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#search-supervisor").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".supervisor-table tr").hide();

            $(".supervisor-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
@endsection