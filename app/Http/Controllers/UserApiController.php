<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;
use Log;
use Auth;
use Hash;
use Storage;
use Setting;
use Exception;
use Notification;
use Carbon\Carbon;

use App\Notifications\Welcome;

use Illuminate\Notifications\Notifiable;

use App\Http\Controllers\SendPushNotification;
use App\Notifications\ResetPasswordOTP;
use App\Helpers\Helper;
use App\Card;
use App\User;
use App\Provider;
use App\Settings;
use App\LoyalityPointGift;
use App\LoyalityPointGiftPurchase;
use App\Promocode;
use App\ServiceType;
use App\UserRequests;
use App\RequestFilter;

use App\PromocodePassbook;


use App\PromocodeUsage;
use App\ProviderService;
use App\UserRequestRating;
use App\Http\Controllers\ProviderResources\TripController;

class UserApiController extends Controller
{

    public function __construct(Request $request)
    {

    }

    public function FareEstimate()
    {

        $UserAPI = new UserApiController;
        $this->UserAPI = $UserAPI;
        $services = $this->UserAPI->services();
        return view('ride', compact('services'));
    }

    public function loyalityGifts()
    {
        $gifts = LoyalityPointGift::where('status', 1)->get();
        if ($gifts->count() > 0) {
            $success['success'] = true;
            $success['response_data'] = $gifts;
            $success['errors'] = 'Gifts available';
            return response()->json($success, 200);
        } else {
            $success['success'] = false;
            $success['response_data'] = [];
            $success['errors'] = 'No gift available';
            return response()->json($success, 200);
        }

    }

    public function userLoyalityGifts($type, $id)
    {
        $purchases = [];
        $purch = LoyalityPointGiftPurchase::where('buyer_id', $id)->where('buyer', $type)->get();
        if ($purch->count() > 0) {
            foreach ($purch as $pk => $p) {
                $purchases[] = [
                    "gift_title" => $p->gift->title,
                    "gift_image" => $p->gift->image,
                    "points_on_purchase" => $p->points_on_purchase,
                    "status" => $p->status
                ];
            }
            $success['success'] = true;
            $success['response_data'] = $purchases;
            $success['errors'] = 'Purchases available';
            return response()->json($success, 200);
        } else {
            $success['success'] = false;
            $success['errors'] = 'No purchase available';
            return response()->json($success, 200);
        }

    }

    public function purchaseLoyalityGifts($type, $id, $gift_id)
    {

        $gift = LoyalityPointGift::where('id', $gift_id)->where('status', 1)->first();
        if ($gift) {

            if ($type == 1) {
                $user = User::where('id', $id)->first();
                if ($user) {
                    if ($user->loyality_points >= $gift->price_in_points) {
                        $loyality = (int)($user->loyality_points - $gift->price_in_points);
                        User::where('id', $user->id)->update([
                            "loyality_points" => $loyality
                        ]);
                        $purchase = new LoyalityPointGiftPurchase;
                        $purchase->buyer = (int)$type;
                        $purchase->buyer_id = (int)$user->id;
                        $purchase->loyality_point_gift_id = (int)$gift->id;
                        $purchase->points_on_purchase = (int)$gift->price_in_points;
                        $purchase->save();

                        $success['success'] = true;
                        $success['errors'] = 'Gift purchase';
                        return response()->json($success, 200);
                    } else {
                        $success['success'] = false;
                        $success['errors'] = 'Not enough points';
                        return response()->json($success, 200);
                    }
                } else {
                    $success['success'] = false;
                    $success['errors'] = 'No customers available';
                    return response()->json($success, 200);
                }
            } else {
                $provider = Provider::where('id', $id)->first();
                if ($provider) {
                    if ($provider->loyality_points >= $gift->price_in_points) {
                        $loyality = (int)($provider->loyality_points - $gift->price_in_points);
                        Provider::where('id', $provider->id)->update([
                            "loyality_points" => $loyality
                        ]);
                        $purchase = new LoyalityPointGiftPurchase;
                        $purchase->buyer = (int)$type;
                        $purchase->buyer_id = (int)$provider->id;
                        $purchase->loyality_point_gift_id = (int)$gift->id;
                        $purchase->points_on_purchase = (int)$gift->price_in_points;
                        $purchase->save();

                        $success['success'] = true;
                        $success['errors'] = 'Gift purchase';
                        return response()->json($success, 200);
                    } else {
                        $success['success'] = false;
                        $success['errors'] = 'Not enough points';
                        return response()->json($success, 200);
                    }
                } else {
                    $success['success'] = false;
                    $success['errors'] = 'No driver available';
                    return response()->json($success, 200);
                }
            }
        } else {
            $success['success'] = false;
            $success['errors'] = 'No gifts found';
            return response()->json($success, 200);
        }

    }


    public function signup(Request $request)
    {

        $this->validate($request, [

            'social_unique_id' => ['required_if:login_by,facebook,google', 'unique:users'],

            'device_type' => 'required|in:android,ios',

            'device_token' => 'required',

            'device_id' => 'required',

            'login_by' => 'required|in:manual,facebook,google',

            'first_name' => 'required|max:255',

            'last_name' => 'required|max:255',

            'email' => 'required|email|max:255|unique:users',

            'mobile' => 'required',

            'password' => 'required|min:6',

        ]);

        try {
            $User = $request->all();
            $User['payment_mode'] = 'CASH';
            $User['password'] = bcrypt($request->password);
            $User = User::create($User);
            return $User;
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            User::where('id', $request->id)->update(['device_id' => '', 'device_token' => '']);
            return response()->json(['message' => trans('api.logout_success')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);

        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function change_password(Request $request)
    {

        $this->validate($request, [

            'password' => 'required|confirmed|min:6',

            'old_password' => 'required',

        ]);

        $User = Auth::user();
        if (Hash::check($request->old_password, $User->password)) {
            $User->password = bcrypt($request->password);
            $User->save();
            if ($request->ajax()) {
                return response()->json(['message' => trans('api.user.password_updated')]);
            } else {
                return back()->with('flash_success', 'Password Updated');
            }
        } else {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.user.change_password')], 500);
            } else {
                return back()->with('flash_error', trans('api.user.change_password'));
            }
        }
    }

    public function update_location(Request $request)
    {
        $this->validate($request, ['latitude' => 'required|numeric', 'longitude' => 'required|numeric',]);
        if ($user = User::find(Auth::user()->id)) {
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->save();
            return response()->json(['message' => trans('api.user.location_updated')]);
        } else {
            return response()->json(['error' => trans('api.user.user_not_found')], 500);
        }
    }

    public function details(Request $request)
    {
        $this->validate($request, ['device_type' => 'in:android,ios',]);
        try {
            if ($user = User::find(Auth::user()->id)) {
                if ($request->has('device_token')) {
                    $user->device_token = $request->device_token;
                }
                if ($request->has('device_type')) {
                    $user->device_type = $request->device_type;
                }
                if ($request->has('device_id')) {
                    $user->device_id = $request->device_id;
                }
                $user->save();
                $user->currency = Setting::get('currency');
                $user->sos = Setting::get('sos_number', '911');
                return $user;
            } else {
                return response()->json(['error' => trans('api.user.user_not_found')], 500);
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function update_profile(Request $request)
    {

        $this->validate($request, [

            'first_name' => 'required|max:255',

            'last_name' => 'max:255',

            'email' => 'email|unique:users,email,' . Auth::user()->id,

            'mobile' => 'required',

            'picture' => 'mimes:jpeg,bmp,png',

        ]);

        try {
            $user = User::findOrFail(Auth::user()->id);
            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }
            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('mobile')) {
                $user->mobile = $request->mobile;
            }
            if ($request->picture != "") {
                Storage::delete($user->picture);
                $user->picture = 'app/public/' . $request->picture->store('user/profile');
            }
            $user->save();
            if ($request->ajax()) {
                return response()->json($user);
            } else {
                return back()->with('flash_success', trans('api.user.profile_updated'));
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => trans('api.user.user_not_found')], 500);
        }
    }

    public function services()
    {
        if ($serviceList = ServiceType::all()) {
            return $serviceList;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 500);

        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function send_request(Request $request)
    {

        $this->validate($request, [

            's_latitude' => 'required|numeric',

            'd_latitude' => 'required|numeric',

            's_longitude' => 'required|numeric',

            'd_longitude' => 'required|numeric',

            'service_type' => 'required|numeric|exists:service_types,id',

            'promo_code' => 'exists:promocodes,promo_code',


            'distance' => 'required|numeric',

            'use_wallet' => 'numeric',

            'payment_mode' => 'required|in:CASH,CARD,PAYPAL',

            'card_id' => ['required_if:payment_mode,CARD', 'exists:cards,card_id,user_id,' . Auth::user()->id],

        ]);

        Log::info('New Request from User: ' . Auth::user()->id);
        Log::info('Request Details:', $request->all());
        $ActiveRequests = UserRequests::PendingRequest(Auth::user()->id)->count();


        if ($ActiveRequests > 0) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.ride.request_inprogress')], 500);
            } else {
                return redirect('dashboard')->with('flash_error', 'A request is already in progress. Try again later');

            }

        }


        $distance = Setting::get('provider_search_radius', '10');
        $latitude = $request->s_latitude;
        $longitude = $request->s_longitude;
        $service_type = $request->service_type;
        $Providers = Provider::with('service')
            ->select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"),
                'id')
            ->where('status', 'approved')
            ->whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
            ->whereHas('service', function ($query) use ($service_type) {
                $query->where('status', 'active');
                $query->where('service_type_id', $service_type);

            })
            ->orderBy('distance')
            ->get();

        // List Providers who are currently busy and add them to the filter list.

        if (count($Providers) == 0) {
            if ($request->ajax()) {

                // Push Notification to User

                return response()->json(['message' => trans('api.ride.no_providers_found')]);
            } else {
                return back()->with('flash_success', 'No Providers Found! Please try again.');
            }
        }
        try {

            $details = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $request->s_latitude . "," . $request->s_longitude . "&destination=" .
                $request->d_latitude . "," . $request->d_longitude . "&mode=driving&key=" . Setting::get('map_key');
            $json = curl($details);
            $details = json_decode($json, TRUE);
            $route_key = $details['routes'][0]['overview_polyline']['points'];
            $UserRequest = new UserRequests;
            $UserRequest->booking_id = Helper::generate_booking_id();
            $UserRequest->user_id = Auth::user()->id;
            $UserRequest->current_provider_id = $Providers[0]->id;
            $UserRequest->service_type_id = $request->service_type;
            $UserRequest->payment_mode = $request->payment_mode;
            $UserRequest->status = 'SEARCHING';
            $UserRequest->s_address = $request->s_address ?: "";
            $UserRequest->d_address = $request->d_address ?: "";
            $UserRequest->s_latitude = $request->s_latitude;


            $UserRequest->s_longitude = $request->s_longitude;
            $UserRequest->d_latitude = $request->d_latitude;
            $UserRequest->d_longitude = $request->d_longitude;
            $UserRequest->distance = $request->distance;
            if (Auth::user()->wallet_balance > 0) {
                $UserRequest->use_wallet = $request->use_wallet ?: 0;

            }

            if ($request->has('eta_total')) {
                $UserRequest->amount = $request->eta_total;
            }


            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->route_key = $route_key;
            if ($Providers->count() <= Setting::get('surge_trigger') && $Providers->count() > 0) {
                $UserRequest->surge = 1;
            }

            $UserRequest->save();
            Log::info('New Request id : ' . $UserRequest->id . ' Assigned to provider : ' . $UserRequest->current_provider_id);

            // update payment mode

            User::where('id', Auth::user()->id)->update(['payment_mode' => $request->payment_mode]);
            if ($request->has('card_id')) {
                Card::where('user_id', Auth::user()->id)->update(['is_default' => 0]);
                Card::where('card_id', $request->card_id)->update(['is_default' => 1]);
            }
            (new SendPushNotification)->IncomingRequest($Providers[0]->id);
            foreach ($Providers as $key => $Provider) {
                $Filter = new RequestFilter;

                // Send push notifications to the first provider

                // incoming request push to provider

                $Filter->request_id = $UserRequest->id;
                $Filter->provider_id = $Provider->id;
                $Filter->save();
            }
            if ($request->ajax()) {

                return response()->json([

                    'message' => 'New request Created!',

                    'request_id' => $UserRequest->id,

                    'current_provider' => $UserRequest->current_provider_id,

                ]);

            } else {
                return redirect('dashboard');
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            } else {
                return back()->with('flash_error', 'Something went wrong while sending request. Please try again.');
            }
        }
    }

    public function cancel_request(Request $request)
    {

        $this->validate($request, ['request_id' => 'required|numeric|exists:user_requests,id,user_id,' . Auth::user()->id,]);
        try {

            $UserRequest = UserRequests::findOrFail($request->request_id);
            if ($UserRequest->status == 'CANCELLED') {
//                (new SendPushNotification)->UserCancellRide($UserRequest);
                if ($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_cancelled')], 500);
                } else {
                    return back()->with('flash_error', 'Request is Already Cancelled!');
                }
            }
            if (in_array($UserRequest->status, ['SEARCHING', 'STARTED', 'ARRIVED'])) {
                if ($UserRequest->status != 'SEARCHING') {
                    $this->validate($request, ['cancel_reason' => 'max:255',]);
                }
                $UserRequest->status = 'CANCELLED';
                $UserRequest->cancel_reason = $request->cancel_reason;
                $UserRequest->cancelled_by = 'USER';
                $UserRequest->save();
                RequestFilter::where('request_id', $UserRequest->id)->delete();


                // Send Push Notification to User
                (new SendPushNotification)->UserCancellRide($UserRequest);
                if ($request->ajax()) {
                    return response()->json(['message' => trans('api.ride.ride_cancelled')]);
                } else {
                    return redirect('dashboard')->with('flash_success', 'Request Cancelled Successfully');
                }
            } else {
                if ($request->ajax()) {
                    return response()->json(['error' => trans('api.ride.already_onride')], 500);
                } else {
                    return back()->with('flash_error', 'Service Already Started!');
                }
            }
        } catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            } else {
                return back()->with('flash_error', 'No Request Found!');
            }
        }
    }

    public function request_status_check()
    {

        try {
            $check_status = ['CANCELLED'];

            $UserRequests = UserRequests::UserRequestStatusCheck(Auth::user()->id, $check_status)
                ->get()
                ->toArray();

            $search_status = ['SEARCHING'];
            $UserRequestsFilter = UserRequests::UserRequestAssignProvider(Auth::user()->id, $search_status)->get();

            $Timeout = Setting::get('provider_select_timeout', 180);
            if (!empty($UserRequestsFilter)) {
                for ($i = 0; $i < sizeof($UserRequestsFilter); $i++) {
                    $ExpiredTime = $Timeout - (time() - strtotime($UserRequestsFilter[$i]->assigned_at));
                    if ($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime < 0) {
                        $Providertrip = new TripController();
                        $Providertrip->assign_next_provider($UserRequestsFilter[$i]->id);
                    } else if ($UserRequestsFilter[$i]->status == 'SEARCHING' && $ExpiredTime > 0) {
                        break;
                    }
                }
            }

            return response()->json(['data' => $UserRequests]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function rate_provider(Request $request)
    {
        $this->validate($request, ['request_id' => 'required|integer|exists:user_requests,id,user_id,' . Auth::user()->id, 'rating' => 'required|integer|in:1,2,3,4,5',
            'comment' => 'max:255',]);
        $UserRequests = UserRequests::where('id', $request->request_id)->where('status', 'COMPLETED')->where('paid', 0)->first();
        if ($UserRequests) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.user.not_paid')], 500);
            } else {
                return back()->with('flash_error', 'Service Already Started!');
            }
        }
        try {
            $UserRequest = UserRequests::findOrFail($request->request_id);
            if ($UserRequest->rating == null) {
                UserRequestRating::create(['provider_id' => $UserRequest->provider_id, 'user_id' => $UserRequest->user_id, 'request_id' => $UserRequest->id,
                    'user_rating' => $request->rating, 'user_comment' => $request->comment,]);
            } else {

                $UserRequest->rating->update([

                    'user_rating' => $request->rating,

                    'user_comment' => $request->comment,

                ]);

            }
            $UserRequest->user_rated = 1;
            $UserRequest->save();
            $average = UserRequestRating::where('provider_id', $UserRequest->provider_id)->avg('user_rating');
            Provider::where('id', $UserRequest->provider_id)->update(['rating' => $average]);

            // Send Push Notification to Provider

            if ($request->ajax()) {
                return response()->json(['message' => trans('api.ride.provider_rated')]);
            } else {
                return redirect('dashboard')->with('flash_success', 'Driver Rated Successfully!');
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            } else {
                return back()->with('flash_error', 'Something went wrong');
            }
        }
    }

    public function trips()
    {

        try {
            $UserRequests = UserRequests::UserTrips(Auth::user()->id)->get();
            if (!empty($UserRequests)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {

                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?" .

                        "autoscale=1" .

                        "&size=320x130" .

                        "&maptype=terrain" .

                        "&format=png" .

                        "&visual_refresh=true" .

                        "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude .

                        "&markers=icon:" . $map_icon . "%7C" . $value->d_latitude . "," . $value->d_longitude .

                        "&path=color:0x191919|weight:3|enc:" . $value->route_key .

                        "&key=" . Setting::get('map_key');

                }

            }

            return $UserRequests;

        } catch (Exception $e) {

            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    public function estimated_fare(Request $request)
    {

        \Log::info('Estimate', $request->all());

        $this->validate($request, [

            's_latitude' => 'required|numeric',

            's_longitude' => 'required|numeric',

            'd_latitude' => 'required|numeric',

            'd_longitude' => 'required|numeric',

            'service_type' => 'required|numeric|exists:service_types,id',

        ]);

        try {

            $details = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $request->s_latitude . "," . $request->s_longitude . "&destinations=" .
                $request->d_latitude . "," . $request->d_longitude . "&mode=driving&sensor=false&key=" . Setting::get('map_key');

            $json = curl($details);
            $details = json_decode($json, TRUE);
            $meter = $details['rows'][0]['elements'][0]['distance']['value'];
            $time = $details['rows'][0]['elements'][0]['duration']['text'];
            $seconds = $details['rows'][0]['elements'][0]['duration']['value'];
            $kilometer = $meter / 1000;

            $minutes = $seconds / 60;

            $hours = $seconds / 3600;
            $tax_percentage = Setting::get('tax_percentage');
            $commission_percentage = Setting::get('commission_percentage');
            $service_type = ServiceType::findOrFail($request->service_type);
            $price = $service_type->fixed;
            $base_distance = $service_type->distance;

            if ($kilometer <= $base_distance) {
                $kilometer = $base_distance;
            }
            if ($service_type->calculator == 'MIN') {
                $price += $service_type->minute * $minutes;
            } else if ($service_type->calculator == 'HOUR') {
                $price += $service_type->minute * 60;
            } else if ($service_type->calculator == 'DISTANCE') {
                $price += ($kilometer * $service_type->price);
            } else if ($service_type->calculator == 'DISTANCEMIN') {
                $price += ($kilometer * $service_type->price) + ($service_type->minute * $minutes);
            } else if ($service_type->calculator == 'DISTANCEHOUR') {
                $price += ($kilometer * $service_type->price) + ($service_type->minute * $minutes * 60);
            } else {
                $price += ($kilometer * $service_type->price);
            }

            //  $price = $finalprice < $price ? $price : $finalprice;

            $tax_price = ($tax_percentage / 100) * $price;
            $total = $price + $tax_price;
            $ActiveProviders = ProviderService::AvailableServiceProvider($request->service_type)->get()->pluck('provider_id');

            $distance = Setting::get('provider_search_radius', '10');

            $latitude = $request->s_latitude;
            $longitude = $request->s_longitude;

            $Providers = Provider::whereIn('id', $ActiveProviders)
                ->where('status', 'approved')
                ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                ->get();
            $surge = 0;
            if ($Providers->count() <= Setting::get('surge_trigger') && $Providers->count() > 0) {
                $surge_price = (Setting::get('surge_percentage') / 100) * $total;
                $total += $surge_price;
                $surge = 1;
            }
            return response()->json(['estimated_fare' => round($price, 2), 'distance' => round($kilometer, 2), 'time' => $time, 'surge' => $surge, 'surge_value' => '0',
                'round_off' => round($tax_price, 2), 'eta_total' => round($total, 2), 'wallet_balance' => Auth::user()->wallet_balance, 'total' => round($total, 2)]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);

        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trip_details(Request $request)
    {

        $this->validate($request, [

            'request_id' => 'required|integer|exists:user_requests,id',

        ]);

        try {
            $UserRequests = UserRequests::UserTripDetails(Auth::user()->id, $request->request_id)->get();
            if (!empty($UserRequests)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {

                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?" .

                        "autoscale=1" .

                        "&size=320x130" .

                        "&maptype=terrain" .

                        "&format=png" .

                        "&visual_refresh=true" .

                        "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude .

                        "&markers=icon:" . $map_icon . "%7C" . $value->d_latitude . "," . $value->d_longitude .

                        "&path=color:0x191919|weight:3|enc:" . $value->route_key .

                        "&key=" . Setting::get('map_key');

                }

            }

            return $UserRequests;
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    public function promocodes()
    {
        try {
            $this->check_expiry();

            return PromocodeUsage::Active()
                ->where('user_id', Auth::user()->id)
                ->with('promocode')
                ->get();

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function check_expiry()
    {
        try {
            $Promocode = Promocode::all();
            foreach ($Promocode as $index => $promo) {
                if (date("Y-m-d") > $promo->expiration) {
                    $promo->status = 'EXPIRED';
                    $promo->save();
                    PromocodeUsage::where('promocode_id', $promo->id)->update(['status' => 'EXPIRED']);
                } else {
//                    PromocodeUsage::where('promocode_id', $promo->id)->update(['status' => 'ADDED']);

                }

            }

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);

        }

    }

    /**
     * add promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function add_promocode(Request $request)
    {

        $this->validate($request, [

            'promocode' => 'required|exists:promocodes,promo_code',

        ]);

        try {
            $find_promo = Promocode::where('promo_code', $request->promocode)->first();


            if ($find_promo->status == 'EXPIRED' || (date("Y-m-d") > $find_promo->expiration)) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => trans('api.promocode_expired'),
                        'code' => 'promocode_expired'
                    ]);
                } else {
                    return back()->with('flash_error', trans('api.promocode_expired'));
                }
            } elseif ($find_promo->max_count <= PromocodeUsage::where('promocode_id', $find_promo->id)->where('status', 'USED')->count()) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'Max Users Used',
                        'code' => 'promocode_already_in_use'
                    ]);
                } else {
                    return back()->with('flash_error', 'Promocode Already in use');
                }
            } elseif (PromocodeUsage::where('promocode_id', $find_promo->id)->where('user_id', Auth::user()->id)->where('status', 'ADDED')->count() > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => trans('api.promocode_already_in_use'),
                        'code' => 'promocode_already_in_use'
                    ]);
                } else {
                    return back()->with('flash_error', 'Promocode Already in use');
                }
            } else {

                $promo = new PromocodeUsage;
                $promo->promocode_id = $find_promo->id;
                $promo->user_id = Auth::user()->id;
                $promo->status = 'ADDED';
                $promo->save();
                if ($request->ajax()) {
                    return response()->json([
                        'message' => trans('api.promocode_applied'),
                        'code' => 'promocode_applied',
                        'amount' => $find_promo->discount
                    ]);
                } else {
                    return back()->with('flash_success', trans('api.promocode_applied'));
                }

            }

        } catch (Exception $e) {

            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            } else {
                return back()->with('flash_error', 'Something Went Wrong');
            }
        }
    }

    public function upcoming_trips()
    {
        try {
            $UserRequests = UserRequests::UserUpcomingTrips(Auth::user()->id)->get();
            if (!empty($UserRequests)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map =
                        "https://maps.googleapis.com/maps/api/staticmap?" . "autoscale=1" . "&size=320x130" . "&maptype=terrain" . "&format=png" .
                        "&visual_refresh=true" . "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude . "&markers=icon:" . $map_icon .
                        "%7C" . $value->d_latitude . "," . $value->d_longitude . "&path=color:0x000000|weight:3|enc:" . $value->route_key . "&key=" .
                        Setting::get('map_key');
                }
            }
            return $UserRequests;
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    public function upcoming_trip_details(Request $request)
    {
        $this->validate($request, ['request_id' => 'required|integer|exists:user_requests,id',]);
        try {
            $UserRequests = UserRequests::UserUpcomingTripDetails(Auth::user()->id, $request->request_id)->get();
            if (!empty($UserRequests)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map =
                        "https://maps.googleapis.com/maps/api/staticmap?" . "autoscale=1" . "&size=320x130" . "&maptype=terrain" . "&format=png" .
                        "&visual_refresh=true" . "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude . "&markers=icon:" . $map_icon .
                        "%7C" . $value->d_latitude . "," . $value->d_longitude . "&path=color:0x000000|weight:3|enc:" . $value->route_key . "&key=" .
                        Setting::get('map_key');
                }
            }
            return $UserRequests;
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    public function show_providers(Request $request)
    {
        $this->validate($request, ['latitude' => 'required|numeric', 'longitude' => 'required|numeric', 'service' => 'numeric|exists:service_types,id',]);
        try {
            $distance = Setting::get('provider_search_radius', '10');
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            if ($request->has('service')) {
                $ActiveProviders = ProviderService::AvailableServiceProvider($request->service)->get()->pluck('provider_id');

                $Providers = Provider::whereIn('id', $ActiveProviders)
                    ->where('status', 'approved')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->get();
            } else {
                $Providers = Provider::where('status', 'approved')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->with("service")->get();
            }
            if (count($Providers) == 0) {
                if ($request->ajax()) {
                    return response()->json(['message' => "No Providers Found"]);
                } else {
                    return back()->with('flash_success', 'No Providers Found! Please try again.');

                }

            }

            return $Providers;
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
            } else {
                return back()->with('flash_error', 'Something went wrong while sending request. Please try again.');

            }

        }

    }

    /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\Response
     */

    public function forgot_password(Request $request)
    {

        $this->validate($request, [

            'email' => 'required|email|exists:users,email',

        ]);

        try {
            $user = User::where('email', $request->email)->first();
            $otp = mt_rand(100000, 999999);
            $user->otp = $otp;
            $user->save();

            // Notification::send($user, new ResetPasswordOTP($otp));

            return response()->json([

                'message' => 'OTP sent to your email!',

                'user' => $user

            ]);

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function reset_password(Request $request)
    {
        $this->validate($request, ['password' => 'required|confirmed|min:6', 'id' => 'required|numeric|exists:users,id']);
        try {
            $User = User::findOrFail($request->id);
            $User->password = bcrypt($request->password);
            $User->save();
            if ($request->ajax()) {
                return response()->json(['message' => 'Password Updated']);
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
        }
    }

    public function verify(Request $request)
    {
        $this->validate($request, ['email' => 'required|email|max:255|unique:users',]);
        try {
            return response()->json(['message' => trans('api.email_available')]);
        } catch (Exception $e) {
            return response()->json(['message' => trans('api.email_available')]);

        }

    }

    public function wallet_passbook(Request $request)
    {
        try {
            return WalletPassbook::where('user_id', Auth::user()->id)->get();
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

    public function promo_passbook(Request $request)
    {
        try {
            return PromocodePassbook::where('user_id', Auth::user()->id)->with('promocode')->get();
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    public function help_details(Request $request)
    {
        try {
            if ($request->ajax()) {
                return response()->json(['contact_number' => Setting::get('contact_number', ''), 'contact_email' => Setting::get('contact_email', '')]);
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
        }
    }
}
