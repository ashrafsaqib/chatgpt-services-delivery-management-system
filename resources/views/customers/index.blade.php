@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Customer ({{ $total_customer }})</h2>
                </div>
                <div class="float-end">
                    <button type="button" class="btn btn-primary mb-2" id="updateAffiliateBtn">Update Affiliate</button>
                    <a class="btn btn-danger mb-2" href="{{ Request::fullUrlWithQuery(['print' => 1]) }}"><i
                            class="fa fa-print"></i> PDF</a>
                    <a href="{{ Request::fullUrlWithQuery(['csv' => 1]) }}" class="btn btn-success mb-2"><i
                            class="fa fa-download"></i> Excel</a>
                    @can('customer-create')
                        <a class="btn btn-success mb-2" href="{{ route('customers.create') }}"> <i class="fa fa-plus"></i>
                            Create</a>
                    @endcan
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="bulk-coupon">Select Coupon</label>
                        </div>
                        <select class="custom-select" id="bulk-coupon" name="bulk-coupon">
                            @foreach ($coupons as $coupon)
                                <option value="{{ $coupon->id }}">{{ $coupon->name }} ({{ $coupon->code }})</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button id="bulkAssignCoupon" class="btn btn-primary" type="button"><i
                                    class="fa fa-save"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="affiliateModal" tabindex="-1" aria-labelledby="affiliateModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="affiliateModalLabel">Update Affiliate</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="affiliateForm">
                                    <div class="mb-3">
                                        <label for="affiliateInput" class="form-label">Affiliate:</label>
                                        <select name="affiliate_id" class="form-control" id="affiliateInput">
                                            <option></option>
                                            @foreach ($affiliates as $affiliate)
                                            @if($affiliate->affiliate->status == 1)
                                                <option value="{{ $affiliate->id }}">{{ $affiliate->name }}
                                                    @if ($affiliate->affiliate->code)
                                                        ({{ $affiliate->affiliate->code }})
                                                    @endif
                                                </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="typeInput" class="form-label">Affiliate Commission type:</label>
                                        <select name="type" class="form-control" id="typeInput">
                                            <option></option>
                                            <option @if (old('type') == 'F') selected @endif value="F">Fix
                                            </option>
                                            <option @if (old('type') == 'P') selected @endif value="P">
                                                Persentage</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="commissionInput" class="form-label">Commission:</label>
                                        <input type="number" name="commission" class="form-control"
                                            placeholder="Affiliate Commission" value="{{ old('commission') }}"
                                            id="commissionInput">
                                    </div>
                                    <div class="mb-3">
                                        <label for="expireDateInput" class="form-label">Expiration Date:</label>
                                        <input type="date" name="expiry_date" class="form-control" id="expireDateInput">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
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
                        <td>
                            <input type="checkbox" onclick="$('input[name*=\'ids\']').prop('checked', this.checked);">
                        </td>
                        <th><a class=" ml-2 text-decoration-none"
                            href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                        @if (request('sort') === 'name')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif
                    </th>
                    <th><a class=" ml-2 text-decoration-none"
                            href="{{ route('customers.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                        @if (request('sort') === 'email')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif
                    </th>
                        <th>Orders</th>
                        <th>Last Order Date</th>
                        <th>Affiliate</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($customers))
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <input type="checkbox" class="item-checkbox" name="ids[{{ ++$i }}]"
                                        value="{{ $customer->id }}">
                                </td>
                                <td class="@if ($customer->status == 1) text-success @else text-danger @endif"> {{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->customer_orders_count }}</td>
                                <td>
                                    @if ($customer->customerOrders->isEmpty())
                                        No orders found.
                                    @else
                                        {{ $customer->customerOrders->last()->created_at->toDateString() }}
                                    @endif
                                </td>
                                <td>{{ $customer->userAffiliate->affiliateUser->name ?? '' }}@if (isset($customer->userAffiliate->affiliate->code))
                                        ({{ $customer->userAffiliate->affiliate->code }})
                                    @endif
                                </td>
                                <td>
                                    <form id="deleteForm{{ $customer->id }}"
                                        action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                                        <a class="btn btn-info" href="{{ route('customers.show', $customer->id) }}"><i
                                                class="fas fa-eye"></i></a>
                                        @can('customer-edit')
                                            <a class="btn btn-primary" href="{{ route('customers.edit', $customer->id) }}"><i
                                                    class="fas fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('customer-delete')
                                            <button type="button" onclick="confirmDelete('{{ $customer->id }}')"
                                                class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                        @endcan
                                        @can('order-list')
                                            <a class="btn btn-info" target="_blank"
                                                href="{{ route('orders.index') }}?customer_id={{ $customer->id }}&date_from={{ $filter['date_from'] ?? '' }}&date_to={{ $filter['date_to'] ?? '' }}">Order
                                                History</a>
                                        @endcan
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#couponModal{{ $customer->id }}">
                                            <i class="fas fa-gift"></i> Apply Coupon
                                        </button>
                                    </form>
                                    <div class="modal fade" id="couponModal{{ $customer->id }}" tabindex="-1"
                                        aria-labelledby="couponModalLabel{{ $customer->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="couponModalLabel{{ $customer->id }}">Select
                                                        Coupon for {{ $customer->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('coupons.assign', $customer->id) }}"
                                                        method="post">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="couponSelect{{ $customer->id }}"
                                                                class="form-label">Select Coupon:</label>
                                                            <select class="form-select"
                                                                id="couponSelect{{ $customer->id }}" name="coupon_id">
                                                                @foreach ($coupons as $coupon)
                                                                    <option value="{{ $coupon->id }}">
                                                                        {{ $coupon->name }} ({{ $coupon->code }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Save Coupon</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">There is no Customer</td>
                        </tr>
                    @endif
                </table>
                {!! $customers->links() !!}
            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('customers.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                <input type="email" name="email" value="{{ $filter['email'] }}"
                                    class="form-control" placeholder="abc@gmail.com">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Number:</strong>
                                <input type="text" name="number" value="{{ $filter['number'] }}"
                                    class="form-control" placeholder="Enter number with country code">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Affiliate:</strong>
                                <select name="affiliate_id" class="form-control">
                                    <option></option>
                                    @foreach ($affiliates as $affiliate)
                                    @if($affiliate->affiliate->status == 1)
                                        <option value="{{ $affiliate->id }}"
                                            @if ($filter['affiliate_id'] == $affiliate->id) selected @endif>{{ $affiliate->name }}
                                            @if ($affiliate->affiliate->code)
                                                ({{ $affiliate->affiliate->code }})
                                            @endif
                                        </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="zone"><strong>Zone:</strong></label>
                                <select name="zone" id="zone" class="form-control">
                                    <option value="">Select a zone</option>
                                    @foreach ($staffZones as $zone)
                                        <option value="{{ $zone->name }}" @if($zone->name == $filter['zone']) selected @endif>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Order Count:</strong>
                                <input type="number" name="order_count" value="{{ $filter['order_count'] }}"
                                    class="form-control" placeholder="Filter by order count">
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <label><i class="fa fa-filter"></i>Filter Customer by Order Created Date</label>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Date From:</strong>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ $filter['date_from'] }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <strong>Date To:</strong>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ $filter['date_to'] }}">
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
        $('#updateAffiliateBtn').click(function() {
            if ($('.item-checkbox:checked').length > 0) {
                $('#affiliateModal').modal('show');
            } else {
                alert('Please select at least one customer.');
            }
        });

        $('#affiliateForm').submit(function(e) {
            e.preventDefault();

            var affiliate = $('#affiliateInput').val();
            var type = $('#typeInput').val();
            var commission = $('#commissionInput').val();
            var expireDate = $('#expireDateInput').val();
            var selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            console.log(affiliate, type, commission, expireDate, selectedItems);
            $.ajax({
                url: '{{ route('customers.updateAffiliate') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    affiliate: affiliate,
                    type: type,
                    commission: commission,
                    expireDate: expireDate,
                    selectedItems: selectedItems
                }),
                success: function(data) {
                    alert(data.message);
                    $('#affiliateModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        alert(errorMessage);
                    } else {
                        console.error('Error:', xhr.responseText);
                        alert('An unexpected error occurred. Please try again later.');
                    }
                }
            });
        });
    </script>

    <script>
        $('#bulkAssignCoupon').click(function() {
            const selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            const coupon_value = $('select[name="bulk-coupon"]').val();
            const coupon_text = $('select[name="bulk-coupon"] option:selected').text();

            if (selectedItems.length > 0) {
                if (confirm("Are you sure you want to assign " + coupon_text + " coupon to selected customer?")) {
                    console.log("adas");
                    bulkAssignCoupon(selectedItems, coupon_value);
                }
            } else {
                alert('Please select Customer to Assign Coupon.');
            }
        });

        function bulkAssignCoupon(selectedItems, coupon_id) {
            $.ajax({
                url: '{{ route('customers.bulkAssignCoupon') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems,
                    coupon_id
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

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
