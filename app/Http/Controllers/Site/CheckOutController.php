<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CustomerProfile;
use App\Models\Holiday;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Console\Command\DumpCompletionCommand;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $booked_services = array();
        $service_ids = Session::get('serviceIds');
        if ($service_ids) {
            foreach ($service_ids as $id) {
                $service =  Service::find($id);
                if ($service) {
                    $booked_services[] = $service;
                }
            }
        }
        //TODO : remove all with i and such page request stuff
        return view('site.checkOut.index', compact('booked_services'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function addToCart(Request $request, $id)
    {

        $serviceId = $id;
        $serviceIds = Session::get('serviceIds', []);

        if (!in_array($serviceId, $serviceIds)) {
            $serviceIds[] = $serviceId;
            Session::put('serviceIds', $serviceIds);
        }

        return redirect()->back()->with('cart-success', 'Service Add to Cart Successfully.');
    }

    public function removeToCart(Request $request, $id)
    {

        $idToRemove = $id;

        $serviceIds = Session::get('serviceIds', []);

        $index = array_search($idToRemove, $serviceIds);

        if ($index !== false) {
            array_splice($serviceIds, $index, 1);
        }

        Session::put('serviceIds', $serviceIds);

        return redirect()->back()->with('success', 'Service Remove to Cart Successfully.');
    }

    public function storeSession(Request $request)
    {
        $this->validate($request, [
            'buildingName' => 'required',
            'area' => 'required',
            'flatVilla' => 'required',
            'street' => 'required',
            'landmark' => 'required',
            'name' => 'required',
            'number' => 'required',
            'email' => 'required|email',
            'whatsapp' => 'required',
            'date' => 'required',
            'service_staff_id' => 'required',
            'affiliate_code' => ['nullable', 'exists:affiliates,code'],
            'gender' => 'required',
            'coupon_code' => [
                'nullable',
                Rule::exists('coupons', 'code')->where(function ($query) {
                    $query->where('status', 1)
                        ->where('date_start', '<=', now())
                        ->where('date_end', '>=', now());
                }),
            ],
        ]);

        if ($request->selected_service_ids) {
            Session::forget('serviceIds');
            foreach ($request->selected_service_ids as $serviceId) {
                $serviceIds[] = $serviceId;
                Session::put('serviceIds', $serviceIds);
            }
        }

        $address = [];

        $address['buildingName'] = $request->buildingName;
        $address['area'] = $request->area;
        $address['flatVilla'] = $request->flatVilla;
        $address['street'] = $request->street;
        $address['landmark'] = $request->landmark;
        $address['city'] = $request->city;
        $address['number'] = config('app.country_code') . $request->number;
        $address['whatsapp'] = config('app.country_code') . $request->whatsapp;
        $address['email'] = $request->email;
        $address['name'] = $request->name;
        $address['searchField'] = $request->searchField;
        $address['update_profile'] = $request->update_profile;
        $address['gender'] = $request->gender;
        if ($request->custom_location && strpos($request->custom_location, ",") != FALSE) {
            [$latitude, $longitude] = explode(",", $request->custom_location);
            $address['latitude'] = $latitude;
            $address['longitude'] = $longitude;
        } else {
            $address['latitude'] = $request->latitude;
            $address['longitude'] = $request->longitude;
        }
        $staff_and_time = [];

        $staff_and_time['date'] = $request->date;
        $staff_id = $request->service_staff_id;
        $time_slot = $request->time_slot_id[$staff_id];
        $staff_and_time['time_slot'] = $time_slot;
        $staff_and_time['service_staff_id'] = $staff_id;


        $code['affiliate_code'] = $request->affiliate_code;
        $code['coupon_code'] = $request->coupon_code;

        if (session()->has('address')) {
            Session::forget('address');
            Session::put('address', $address);
        } else {
            Session::put('address', $address);
        }

        if (session()->has('staff_and_time')) {
            Session::forget('staff_and_time');
            Session::put('staff_and_time', $staff_and_time);
        } else {
            Session::put('staff_and_time', $staff_and_time);
        }

        if (session()->has('code')) {
            Session::forget('code');
            Session::put('code', $code);
        } else {
            Session::put('code', $code);
        }
        cookie()->queue('address', json_encode($address), 5256000);
        cookie()->queue('staff_and_time', json_encode($staff_and_time), 5256000);
        cookie()->queue('code', json_encode($code), 5256000);

        return redirect('confirmStep');
    }

    public function bookingStep(Request $request)
    {
        // TODO check cookie if works 
        if ($request->cookie('address') !== null) {
            $addresses = json_decode($request->cookie('address'), true);
            // if (Session::get('address')) {
            //     $addresses = Session::get('address');
        } else {
            $addresses = [
                'buildingName' => '',
                'area' => '',
                'flatVilla' => '',
                'street' => '',
                'landmark' => '',
                'city' => '',
                'number' => '',
                'whatsapp' => '',
                'email' => '',
                'name' => '',
                'latitude' => '',
                'longitude' => '',
                'searchField' => '',
                'gender' => '',
            ];
        }

        if ($request->cookie('affiliate_id')) {
            $affiliate = Affiliate::where('user_id', $request->cookie('affiliate_id'))->first();
            $url_affiliate_code = $affiliate->code;
        } else {
            $url_affiliate_code = '';
        }
        if ($request->cookie('code') !== null) {
            $code = json_decode($request->cookie('code'), true);
            // if (session()->has('code')) {
            //     $code = Session::get('code');
            $affiliate_code = $code['affiliate_code'];
            $coupon_code = $code['coupon_code'];
        } else {
            $affiliate_code = '';
            $coupon_code = '';
        }

        if (Auth::check()) {
            $email = Auth::user()->email;
            $name = Auth::user()->name;
        } else {
            $email = $addresses['email'];
            $name = $addresses['name'];
        }

        if (session()->has('serviceIds')) {
            $serviceIds = Session::get('serviceIds');
            $selectedServices = Service::whereIn('id', $serviceIds)->get();
        } else {
            $selectedServices = [];
            $serviceIds = [];
        }

        $date = date('Y-m-d');
        if ($addresses['area']) {
            $area = $addresses['area'];
        } else {
            $area = session('address') ? session('address')['area'] : '';
        }

        $categories = ServiceCategory::get();
        $services = Service::get();

        $city = $addresses['city'];
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date);
        return view('site.checkOut.bookingStep', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'email', 'name', 'addresses', 'affiliate_code', 'coupon_code', 'url_affiliate_code', 'selectedServices', 'categories', 'services', 'serviceIds'));
    }

    public function confirmStep(Request $request)
    {
        $requiredSessionKeys = ['staff_and_time', 'address', 'serviceIds'];
        $missingKeys = array_diff($requiredSessionKeys, array_keys(Session::all()));

        if (!empty($missingKeys)) {
            if (!Session::has('serviceIds')) {
                $errorMessage = "You have not added any service to cart.";
            } else {
                $errorMessage = "There is no " . implode(", ", $missingKeys);
            }
            return redirect('/')->with('error', $errorMessage);
        }

        $staff_and_time = Session::get('staff_and_time');
        $address = Session::get('address');
        $serviceIds = Session::get('serviceIds');
        $code = Session::get('code');
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($address['area']) . "%"])->first();

        $services = Service::whereIn('id', $serviceIds)->get();
        $time_slot = TimeSlot::find($staff_and_time['time_slot']);
        $staff = User::find($staff_and_time['service_staff_id']);

        $sub_total = $services->sum(function ($service) {
            return isset($service->discount) ? $service->discount : $service->price;
        });

        if ($code['coupon_code']) {
            $coupon = Coupon::where('code', $code['coupon_code'])->first();
            $coupon_id = $coupon->id;
            if ($coupon->type == "Percentage") {
                $coupon_discount = ($sub_total * $coupon->discount) / 100;
            } else {
                $coupon_discount = $coupon->discount;
            }
        } else {
            $coupon_discount = 0;
        }

        $staff_charges = $staff->staff->charges ?? 0;
        $transport_charges = $staffZone->transport_charges ?? 0;
        $total_amount = $sub_total + $staff_charges + $transport_charges - $coupon_discount;

        return view('site.checkOut.confirmStep', compact(
            'services',
            'time_slot',
            'address',
            'staff',
            'staff_and_time',
            'staffZone',
            'code',
            'sub_total',
            'staff_charges',
            'transport_charges',
            'total_amount',
            'coupon_discount',
        ));
    }


    public function slots(Request $request)
    {
        if ($request->has('order_id') && (int)$request->order_id) {
            $order = Order::find($request->order_id);
            $area = $order->area;
            $date = $order->date;
        } else {
        }
        if ($request->has('area')) {
            $area = $request->area;
        }
        if ($request->has('date')) {
            $date = $request->date;
        }
        if (!isset($area)) {
            $address = Session::get('address');
            $area = $address ? $address['area'] : '';
        }
        $order_id = $request->has('order_id') && (int)$request->order_id ? $request->order_id : NULL;
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date, $order_id);
        return view('site.checkOut.timeSlots', compact('timeSlots', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'area', 'date'));
    }
}
