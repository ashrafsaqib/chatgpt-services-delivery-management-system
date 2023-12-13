<?php

namespace App\Http\Controllers\AppController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CouponHistory;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\OrderTotal;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;

class CustomerController extends Controller

{
    public function __construct()
    {
        $this->middleware('log.api');
    }

    public function login(Request $request)
    {
        $credentials = [
            "email" => strtolower(trim($request->username)),
            "password" => $request->password
        ];
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->username)->first();

            if ($request->has('fcmToken') && $request->fcmToken) {
                $user->device_token = $request->fcmToken;
                $user->save();
            }

            $token = $user->createToken('app-token')->plainTextToken;
            $user_info = CustomerProfile::where('user_id', $user->id)->first();

            return response()->json([
                'user' => $user,
                'user_info' => $user_info,
                'access_token' => $token,
            ], 200);
        }

        return response()->json(['error' => 'These credentials do not match our records.'], 401);
    }

    public function updateCustomerInfo(Request $request)
    {
        CustomerProfile::where('user_id', $request->user_id)->update($request->all());
        return response()->json([
            'msg' => "Updated Successfully!",
        ], 200);
    }

    public function signup(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'affiliate' => ['nullable', 'exists:affiliates,code'],
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        // If validation passes, proceed with creating the user
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['email'] = strtolower(trim($input['email']));

        $user = User::create($input);
        $user->assignRole("Customer");

        if ($request->affiliate) {
            $affiliate = Affiliate::where('code', $request->affiliate)->first();

            $user->affiliates()->attach($affiliate->user_id);
        }

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ], 200);
    }

    public function index()
    {
        $staffZones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        $slider_images = Setting::where('key', 'Slider Image')->value('value');
        $featured_services = Setting::where('key', 'Featured Services')->value('value');

        $featured_services = explode(",", $featured_services);

        $whatsapp_number = Setting::where('key', 'WhatsApp Number For Customer App')->value('value');
        $images = explode(",", $slider_images);

        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->whereIn('category_id', $categories->pluck('id')->toArray())->orderBy('name', 'ASC')->get();
        $categoriesArray = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'image' => $category->image,
                'icon' => $category->icon
            ];
        })->toArray();

        $servicesArray = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'price' => $service->price,
                'discount' => $service->discount,
                'duration' => $service->duration,
                'category_id' => $service->category_id,
                'rating' => $service->averageRating()
            ];
        })->toArray();

        $staffs = User::role('Staff')
            ->whereHas('staff', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('name', 'ASC')
            ->with('staff')
            ->get();

        $staffs->map(function ($staff) {
            $staff->rating = $staff->averageRating();
            return $staff;
        });

        return response()->json([
            'images' => $images,
            'categories' => $categoriesArray,
            'services' => $servicesArray,
            'featured_services' => $featured_services,
            'staffZones' => $staffZones,
            'staffs' => $staffs,
            'whatsapp_number' => $whatsapp_number
        ], 200);
    }

    public function filterServices(Request $request)
    {
        if ($request->category_id) {
            $services = Service::where('status', 1)->where('category_id', $request->category_id)->orderBy('name', 'ASC')->get();
        }

        if ($request->filter) {
            $services = Service::where('status', 1)->where('name', 'like', '%' . $request->filter . '%')->orderBy('name', 'ASC')->get();
        }
        return response()->json([
            'services' => $services,
        ], 200);
    }

    public function getServiceDetails(Request $request)
    {
        if ($request->service_id) {
            $services = Service::where('status', 1)->where('id', $request->service_id)->orderBy('name', 'ASC')->first();
        }

        return response()->json([
            'services' => $services
        ], 200);
    }

    public function availableTimeSlot(Request $request)
    {
        try {
            $transportCharges = StaffZone::where('name', $request->area)->value('transport_charges');
            [$timeSlots, $staffIds, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($request->area, $request->date);
            $availableStaff = [];
            $staffDisplayed = [];
            $staffSlots = [];

            foreach ($timeSlots as $timeSlot) {
                $staffCounter = 0;
                $holidayCounter = 0;
                $bookedCounter = 0;

                foreach ($timeSlot->staffs as $staff) {
                    if (!in_array($staff->id, $staffIds)) {
                        $bookedCounter++;
                    }
                    if (!in_array($staff->id, $timeSlot->excluded_staff)) {
                        $holidayCounter++;
                    }
                    if (!in_array($staff->id, $staffIds) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                        $staffCounter++;
                        $currentSlot = [$timeSlot->id, date('h:i A', strtotime($timeSlot->time_start)) . '-- ' . date('h:i A', strtotime($timeSlot->time_end)), $timeSlot->id];

                        if (isset($staffSlots[$staff->id])) {
                            array_push($staffSlots[$staff->id], $currentSlot);
                        } else {
                            $staffSlots[$staff->id] = [$currentSlot];
                        }

                        if (!in_array($staff->id, $staffDisplayed)) {
                            $staffDisplayed[] = $staff->id;
                            $availableStaff[] = $staff;
                        }
                    }
                }
            }

            if (count($staffDisplayed) == 0) {
                return response()->json([
                    'msg' => "Whoops! No Staff Available",
                ], 201);
            }

            $availableStaff = collect($availableStaff)->map(function ($staff) {
                $staff->rating = $staff->averageRating();
                return $staff;
            });

            return response()->json([
                'transport_charges' => $transportCharges,
                'availableStaff' => $availableStaff,
                'slots' => $staffSlots,
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions (log or return an error response)
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function addOrder(Request $request)
    {
        $password = NULL;
        $input = $request->all();
        $has_order = Order::where('service_staff_id', $input['service_staff_id'])->where('date', $input['date'])->where('time_slot_id', $input['time_slot_id'])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();

        if (count($has_order) == 0) {

            $staff = User::find($input['service_staff_id']);

            $input['status'] = "Pending";
            $input['driver_status'] = "Pending";
            $input['staff_name'] = $staff->name;
            $input['time_slot_id'] = $input['time_slot_id'];
            $input['driver_id'] = $staff->staff->driver_id;

            $user = User::where('email', $input['email'])->first();

            if (isset($user)) {

                $user->customerProfile()->create($input);
                $input['customer_id'] = $user->id;
                $customer_type = "Old";
            } else {
                $customer_type = "New";

                $password = $input['number'];

                $input['password'] = Hash::make($password);

                $user = User::create($input);

                $user->customerProfile()->create($input);

                $input['customer_id'] = $user->id;

                $user->assignRole('Customer');
            }

            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();

            $services = Service::whereIn('id', $input['service_ids'])->get();

            $sub_total = $services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });

            if ($request->coupon_id) {
                $coupon = Coupon::find($request->coupon_id);
                $input['coupon_id'] = $coupon->id;
                if ($coupon->type == "Percentage") {
                    $discount = ($sub_total * $coupon->discount) / 100;
                } else {
                    $discount = $coupon->discount;
                }
            } else {
                $discount = 0;
            }

            $staff_charges = $staff->staff->charges ?? 0;
            $transport_charges = $staffZone->transport_charges ?? 0;
            $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

            $input['sub_total'] = (int)$sub_total;
            $input['discount'] = (int)$discount;
            $input['staff_charges'] = (int)$staff_charges;
            $input['transport_charges'] = (int)$transport_charges;
            $input['total_amount'] = (int)$total_amount;

            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;
            $input['payment_method'] = "Cash-On-Delivery";
            $input['customer_name'] = $input['name'];
            $input['customer_email'] = $input['email'];

            $order = Order::create($input);

            $input['order_id'] = $order->id;
            $input['discount_amount'] = $input['discount'];

            OrderTotal::create($input);

            if ($request->coupon_id) {
                CouponHistory::create($input);
            }

            foreach ($input['service_ids'] as $id) {
                $services = Service::find($id);
                $input['service_id'] = $id;
                $input['service_name'] = $services->name;
                $input['duration'] = $services->duration;
                $input['status'] = 'Open';
                if ($services->discount) {
                    $input['price'] = $services->discount;
                } else {
                    $input['price'] = $services->price;
                }
                OrderService::create($input);
            }

            if (Carbon::now()->toDateString() == $input['date']) {
                $staff->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                if ($staff->staff->driver) {
                    $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                }
                try {
                    $this->sendOrderEmail($input['order_id'], $input['email']);
                } catch (\Throwable $th) {
                    //TODO: log error or queue job later
                }
            }
            try {
                $this->sendAdminEmail($input['order_id'], $input['email']);
                $this->sendCustomerEmail($input['customer_id'], $customer_type, $input['order_id']);
            } catch (\Throwable $th) {
                //TODO: log error or queue job later
            }

            return response()->json([
                'msg' => "Order created successfully.",
                'date' => $order->date,
                'staff' => $order->staff_name,
                'slot' => $order->time_slot_value,
                'total_amount' => $order->total_amount,
            ], 200);
        } else {
            return response()->json([
                'msg' => "Sorry! Unfortunately This slot was booked by someone else just now."
            ], 201);
        }
    }

    public function getOrders(Request $request)
    {

        $orders = Order::where('customer_id', $request->user_id)->orderBy('date', 'DESC')->with('orderServices.service')->with('order_total')->get();

        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function editOrder(Request $request)
    {

        $order = Order::find($request->id);
        $orderTotal = OrderTotal::where('order_id', $request->id)->first();
        $transport_charges = StaffZone::where('name', $order->area)->value('transport_charges');

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $request->id);

        $availableStaff = [];
        $staff_displayed = [];
        $staff_slots = [];
        foreach ($timeSlots as $timeSlot) {
            $staff_counter = 0;
            $holiday_counter = 0;
            $booked_counter = 0;
            foreach ($timeSlot->staffs as $staff) {
                if (!in_array($staff->id, $staff_ids)) {
                    $booked_counter++;
                }
                if (!in_array($staff->id, $timeSlot->excluded_staff)) {
                    $holiday_counter++;
                }
                if (!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                    $staff_counter++;
                    $current_slot = [$timeSlot->id,  date('h:i A', strtotime($timeSlot->time_start)) . '-- ' . date('h:i A', strtotime($timeSlot->time_end)), $timeSlot->id];

                    if (isset($staff_slots[$staff->id])) {
                        array_push($staff_slots[$staff->id], $current_slot);
                    } else {
                        $staff_slots[$staff->id] = [$current_slot];
                    }
                    if (!in_array($staff->id, $staff_displayed)) {
                        $staff_displayed[] = $staff->id;
                        $availableStaff[] = $staff;
                    }
                }
            }
        }
        if (count($staff_displayed) == 0) {
            return response()->json([
                'msg' => "Whoops! No Staff Available",
            ], 201);
        }

        return response()->json([
            'transport_charges' => $transport_charges,
            'availableStaff' => $availableStaff,
            'orderTotal' => $orderTotal,
            'slots' => $staff_slots,
            'order' => $order,
        ], 200);
    }

    public function updateOrder(Request $request)
    {
        $input = $request->all();
        $time_slot = TimeSlot::find($request->time_slot_id);
        $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));
        $user = User::find($request->service_staff_id);
        $order = Order::find($request->id);
        $input['staff_name'] = $user->name;

        if ($user->staff->charges) {
            $input['total_amount'] = ($order->total_amount - $order->order_total->staff_charges) + $user->staff->charges;
            $order->order_total->staff_charges = $user->staff->charges;
            $order->order_total->save();
        }

        $order->update($input);

        return response()->json([
            'msg' => "Order Updated Successfully!"
        ], 200);
    }

    public function getZones()
    {

        $staffZones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        return response()->json([
            'staffZones' => $staffZones
        ], 200);
    }

    public function applyCouponAffiliate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon' => [
                'nullable',
                Rule::exists('coupons', 'code')->where(function ($query) {
                    $query->where('status', 1)
                        ->where('date_start', '<=', now())
                        ->where('date_end', '>=', now());
                }),
            ],
            'affiliate' => ['nullable', 'exists:affiliates,code'],
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        } else {
            $affiliate_id = Affiliate::where('code', $request->affiliate)->value('user_id');
            $coupon = Coupon::where('code', $request->coupon)->first();
        }

        return response()->json([
            'affiliate_id' => $affiliate_id,
            'coupon' => $coupon,
        ], 200);
    }

    public function downloadPDF(Request $request, $id)
    {
        $order = Order::find($id);

        $pdf = app('dompdf.wrapper')->loadView('site.orders.pdf', compact('order'));

        $pdfContent = $pdf->output();

        return response()->json([
            'order' => $order,
            'pdf_content' => base64_encode($pdfContent),
        ], 200);
    }
}
