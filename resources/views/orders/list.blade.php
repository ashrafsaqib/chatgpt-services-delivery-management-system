<table class="table-striped table-bordered table-responsive table">
    <tr>
        <th>Order #</th>
        <th>Staff</th>
        <th><i class="fas fa-clock"></i> Appointment Date</th>
        <th><i class="fas fa-clock"></i> Slots</th>

        @if (auth()->user()->getRoleNames() == '["Supervisor"]')
            <th>Landmark</th>
            <th>Area</th>
            <th>City</th>
            <th>Building name</th>
        @else
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Comment</th>
        @endif
        <th>Status</th>
        <th>Date Added</th>
        <th style="min-width:160px">Action</th>
    </tr>
    @if (count($orders))
        @foreach ($orders as $order)
            <tr>
                <th>
                    @can('order-view')<a href="{{ route('orders.show', $order->id) }}">@endcan
                        #{{ $order->id }}
                    @can('order-view')</a>@endcan
                </th>
                <td>{{ $order->staff_name }}</td>
                <td>{{ $order->date }}</td>
                <td>{{ $order->time_slot_value }}</td>
                @if (auth()->user()->getRoleNames() == '["Supervisor"]')
                    <td>{{ $order->landmark }}</td>
                    <td>{{ $order->area }}</td>
                    <td>{{ $order->city }}</td>
                    <td>{{ $order->buildingName }}</td>
                @else
                    <td>{{ $order->customer_name }}</td>
                    <td>@currency($order->total_amount)</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>{{ substr($order->order_comment, 0, 50) }}...</td>
                @endif
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at }}</td>
                <td>
                    @can('order-edit')
                        <!-- <a class="btn btn-primary" href="{{ route('orders.edit', $order->id) }}">
                            <i class="fas fa-edit"></i>
                        </a> -->
                        <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class=" btn btn-primary" href="#"  data-bs-toggle="dropdown">
                                <i class="fas fa-bars"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @can('order-booking-edit')
                                <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=booking">Booking Edit</a>
                                @endcan
                                @can('order-status-edit')
                                @if(auth()->user()->getRoleNames() == '["Supervisor"]' && $order->status == 'Pending')
                                <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                                @elseif(auth()->user()->getRoleNames() != '["Supervisor"]')
                                <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                                @endif
                                @endcan
                                @can('order-detail-edit')
                                <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=address">Address Edit</a>
                                @endcan
                            </div>
                        </li>
                        </ul>
                    @endcan
                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        @can('order-delete')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endcan
                    </form>

                    @if ($order->status !== 'Complete' && Auth::User()->getRoleNames() == '["Staff"]')
                        @if ($order->status == 'Pending')
                            <a class="btn btn-sm btn-success"
                                href="{{ route('updateOrderStatus', $order->id) }}?status=Accepted">
                                <i class="fas fa-thumbs-up"></i>
                            </a>

                            <a class="btn btn-sm btn-danger"
                                href="{{ route('updateOrderStatus', $order->id) }}?status=Rejected">
                                <i class="fas fa-thumbs-down"></i>
                            </a>
                        @endif
                        @if ($order->status == 'Accepted')
                            <a class="btn btn-sm btn-success"
                                href="{{ route('updateOrderStatus', $order->id) }}?status=Complete"><i
                                    class="fas fa-check-circle"></i></a>
                        @endif
                    @endif

                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="11" class="text-center"> There is no Order</td>
        </tr>
    @endif
</table>
