<?php

namespace App\Http\Controllers\ProviderResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Log;
use Setting;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Controllers\SendPushNotification;

use App\User;
use App\Admin;
use App\Provider;
use App\Promocode;
use App\UserRequests;
use App\RequestFilter;
use App\PromocodeUsage;
use App\PromocodePassbook;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\ServiceType;
use App\WalletPassbook;
use Location\Coordinate;
use Location\Distance\Vincenty;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip INDEX".__LINE__]);
        try {
            if ($request->ajax()) {
                $Provider = Auth::user();
            } else {
                $Provider = Auth::guard('provider')->user();
            }

            $provider = $Provider->id;

            $AfterAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function ($query) use ($provider) {
                    $query->where('status', '<>', 'CANCELLED');
                    $query->where('provider_id', $provider);
                    $query->where('current_provider_id', $provider);
                });

            if (Setting::get('broadcast_request', 0) == 1) {
                $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                    ->where('provider_id', $provider)
                    ->whereHas('request', function ($query) use ($provider) {
                        $query->where('status', '<>', 'CANCELLED');
                        $query->where('current_provider_id', 0);
                    });
            } else {
                $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                    ->where('provider_id', $provider)
                    ->whereHas('request', function ($query) use ($provider) {
                        $query->where('status', '<>', 'CANCELLED');
                        $query->where('current_provider_id', $provider);
                    });
            }

            $IncomingRequests = $BeforeAssignProvider->union($AfterAssignProvider)->get();
            if (!empty($request->latitude)) {
                $Provider->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }
            if (Setting::get('manual_request', 0) == 0) {

                $Timeout = Setting::get('provider_select_timeout', 180);
                if (!empty($IncomingRequests)) {
                    for ($i = 0; $i < sizeof($IncomingRequests); $i++) {
                        $IncomingRequests[$i]->time_left_to_respond = $Timeout - (time() - strtotime($IncomingRequests[$i]->request->assigned_at));
                        if ($IncomingRequests[$i]->request->status == 'SEARCHING' && $IncomingRequests[$i]->time_left_to_respond < 0) {
                            if (Setting::get('broadcast_request', 0) == 1) {
                                $this->assign_destroy($IncomingRequests[$i]->request->id);
                            } else {
                                $this->assign_next_provider($IncomingRequests[$i]->request->id);
                            }
                        }
                    }
                }

            }


            $Response = [
                'account_status' => $Provider->status,
                'service_status' => $Provider->service ? Auth::user()->service->status : 'offline',
                'requests' => $IncomingRequests,
            ];

            return $Response;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * Calculate distance between two coordinates.
     *
     * @return \Illuminate\Http\Response
     */

    public function calculate_distance(Request $request, $id)
    {

//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip calculate_distance"]);
        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
        try {

            if ($request->ajax()) {
                $Provider = Auth::user();
            } else {
                $Provider = Auth::guard('provider')->user();
            }

            $UserRequest = UserRequests::where('status', 'PICKEDUP')
                ->where('provider_id', $Provider->id)
                ->find($id);

            if ($UserRequest && ($request->latitude && $request->longitude)) {
                Log::info("REQUEST ID:" . $UserRequest->id . "==SOURCE LATITUDE:" . $UserRequest->track_latitude . "==SOURCE LONGITUDE:" . $UserRequest->track_longitude);

                if ($UserRequest->track_latitude && $UserRequest->track_longitude) {
//                    \DB::table("temp_table")->insert(["text" => print_r($UserRequest->track_latitude,  true), 'ip' => "At Trip".__LINE__]);
//                    \DB::table("temp_table")->insert(["text" => print_r($UserRequest->track_longitude,  true), 'ip' => "At Trip".__LINE__]);
                    $coordinate1 = new Coordinate($UserRequest->track_latitude, $UserRequest->track_longitude);
                    /** Set Distance Calculation Source Coordinates ****/
                    $coordinate2 = new Coordinate($request->latitude, $request->longitude);
                    /** Set Distance calculation Destination Coordinates ****/
                    $calculator = new Vincenty();
                    /***Distance between two coordinates using spherical algorithm (library as mjaschen/phpgeo) ***/

                    $mydistance = $calculator->getDistance($coordinate1, $coordinate2);
                    $meters = round($mydistance);
                    Log::info("REQUEST ID:" . $UserRequest->id . "==BETWEEN TWO COORDINATES DISTANCE:" . $meters . " (m)");

                    if ($meters >= 100) {
                        /*** If traveled distance riched houndred meters means to be the source coordinates ***/
                        $traveldistance = round(($meters / 1000), 8);

                        $calulatedistance = $UserRequest->track_distance + $traveldistance;

                        $UserRequest->track_distance = $calulatedistance;
                        $UserRequest->distance = $calulatedistance;
                        $UserRequest->track_latitude = $request->latitude;
                        $UserRequest->track_longitude = $request->longitude;
                        $UserRequest->save();

//                        \DB::table("temp_table")->insert(["text" => print_r($UserRequest->track_latitude,  true), 'ip' => "At Trip".__LINE__]);
//                        \DB::table("temp_table")->insert(["text" => print_r($UserRequest->track_longitude,  true), 'ip' => "At Trip".__LINE__]);
                    }
                } else if (!$UserRequest->track_latitude && !$UserRequest->track_longitude) {
//                    \DB::table("temp_table")->insert(["text" => print_r($UserRequest->track_latitude,  true), 'ip' => "At Trip".__LINE__]);
//                    \DB::table("temp_table")->insert(["text" => print_r($UserRequest->track_longitude,  true), 'ip' => "At Trip".__LINE__]);
                    $UserRequest->distance = 0;
                    $UserRequest->track_latitude = $request->latitude;
                    $UserRequest->track_longitude = $request->longitude;
                    $UserRequest->save();
                }
            }
            return $UserRequest;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * Cancel given request.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip cancel".__LINE__]);
        $this->validate($request, [
            'cancel_reason' => 'max:255',
        ]);

        try {

            $UserRequest = UserRequests::findOrFail($request->id);
            $Cancellable = ['SEARCHING', 'ACCEPTED', 'ARRIVED', 'STARTED', 'CREATED'];

            if (!in_array($UserRequest->status, $Cancellable)) {
                return back()->with(['flash_error' => 'Cannot cancel request at this stage!']);
            }

            $UserRequest->status = "CANCELLED";
            $UserRequest->cancel_reason = $request->cancel_reason;
            $UserRequest->cancelled_by = "PROVIDER";
            $UserRequest->save();

            RequestFilter::where('request_id', $UserRequest->id)->delete();

            ProviderService::where('provider_id', $UserRequest->provider_id)->update(['status' => 'active']);

            // Send Push Notification to User
            (new SendPushNotification)->ProviderCancellRide($UserRequest);

            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rate(Request $request, $id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip rate".__LINE__]);
        $this->validate($request, [
            'rating' => 'required|integer|in:1,2,3,4,5',
            'comment' => 'max:255',
        ]);

        try {

            $UserRequest = UserRequests::where('id', $id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

            if ($UserRequest->rating == null) {
                UserRequestRating::create([
                    'provider_id' => $UserRequest->provider_id,
                    'user_id' => $UserRequest->user_id,
                    'request_id' => $UserRequest->id,
                    'provider_rating' => $request->rating,
                    'provider_comment' => $request->comment,
                ]);
            } else {
                $UserRequest->rating->update([
                    'provider_rating' => $request->rating,
                    'provider_comment' => $request->comment,
                ]);
            }

            $UserRequest->update(['provider_rated' => 1]);

            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $id)->delete();

            ProviderService::where('provider_id', $UserRequest->provider_id)->update(['status' => 'active']);

            // Send Push Notification to Provider
            $average = UserRequestRating::where('provider_id', $UserRequest->provider_id)->avg('provider_rating');

            $UserRequest->user->update(['rating' => $average]);

            return response()->json(['message' => 'Request Completed!']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not yet completed!'], 500);
        }
    }


    /**
     * Get the trip history of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip history".__LINE__]);
        if ($request->ajax()) {

            $Jobs = UserRequests::where('provider_id', Auth::user()->id)
                ->where('status', 'COMPLETED')
                ->orderBy('created_at', 'desc')
                ->with('payment')
                ->get();

            if (!empty($Jobs)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?" .
                        "autoscale=1" .
                        "&size=320x130" .
                        "&maptype=terrain" .
                        "&format=png" .
                        "&visual_refresh=true" .
                        "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude .
                        "&markers=icon:" . $map_icon . "%7C" . $value->d_latitude . "," . $value->d_longitude .
                        "&path=color:0x000000|weight:3|enc:" . $value->route_key .
                        "&key=" . Setting::get('map_key');
                }
            }
            return $Jobs;
        }
        $Jobs = UserRequests::where('provider_id', Auth::guard('provider')->user()->id)->with('user', 'service_type', 'payment', 'rating')->get();
        return view('provider.trip.index', compact('Jobs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip accept".__LINE__]);
        try {

            $UserRequest = UserRequests::findOrFail($id);

            if ($UserRequest->status != "SEARCHING") {
                return response()->json(['error' => 'Request already under progress!']);
            }

            $UserRequest->provider_id = Auth::user()->id;

            if (Setting::get('broadcast_request', 0) == 1) {
                $UserRequest->current_provider_id = Auth::user()->id;
            }

            else {


                $UserRequest->status = "STARTED";
                $UserRequest->save();


                ProviderService::where('provider_id', $UserRequest->provider_id)->update(['status' => 'riding']);

                $Filters = RequestFilter::where('request_id', $UserRequest->id)->where('provider_id', '!=', Auth::user()->id)->get();
                // dd($Filters->toArray());
                foreach ($Filters as $Filter) {
                    $Filter->delete();
                }
            }


            // Send Push Notification to User
            (new SendPushNotification)->RideAccepted($UserRequest);

            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to accept, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip update".__LINE__]);
        $this->validate($request, [
            'status' => 'required|in:ACCEPTED,STARTED,ARRIVED,PICKEDUP,DROPPED,PAYMENT,COMPLETED',
        ]);

        try {

            $UserRequest = UserRequests::with('user')->findOrFail($id);
            if ($request->status == 'DROPPED' && $UserRequest->payment_mode != 'CASH') {
                $UserRequest->status = 'COMPLETED';
            } else if ($request->status == 'COMPLETED' && $UserRequest->payment_mode == 'CASH') {



                $provider_loyality = \DB::table('providers')->where('id', $UserRequest->provider_id)->first();
                \DB::table('providers')->where('id', $provider_loyality->id)->update([
                    'loyality_points' => $provider_loyality->loyality_points + Setting::get('loyality_point_per_ride', 0),
                ]);

                $user_loyality = \DB::table('users')->where('id', $UserRequest->user_id)->first();
                \DB::table('users')->where('id', $user_loyality->id)->update([
                    'loyality_points' => $user_loyality->loyality_points + Setting::get('loyality_point_per_ride', 0),
                ]);


                $userRequestPayment = UserRequestPayment::where('request_id', $id)->first();
                $commision = $userRequestPayment->provider_commission;
                $provider = Provider::findOrFail($UserRequest->current_provider_id);
                $provider->wallet_balance = $provider->wallet_balance - $commision;

                $provider->save();
                $UserRequest->status = $request->status;
                $UserRequest->paid = 1;
                // ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);
            } else {
                $UserRequest->status = $request->status;

                if ($request->status == 'ARRIVED') {
                    (new SendPushNotification)->Arrived($UserRequest);
                }
            }

            if ($request->status == 'PICKEDUP') {
                if ($UserRequest->is_track == "YES") {
                    $UserRequest->distance = 0;
                }
                $UserRequest->started_at = Carbon::now();
            }

            $UserRequest->save();

            if ($request->status == 'DROPPED') {
                if ($UserRequest->is_track == "YES") {
                    $UserRequest->d_latitude = $request->latitude ?: $UserRequest->d_latitude;
                    $UserRequest->d_longitude = $request->longitude ?: $UserRequest->d_longitude;
                    $UserRequest->d_address = $request->address ?: $UserRequest->d_address;
                }
                $UserRequest->finished_at = Carbon::now();
                $StartedDate = date_create($UserRequest->started_at);
                $FinisedDate = Carbon::now();
                $TimeInterval = date_diff($StartedDate, $FinisedDate);
                $MintuesTime = $TimeInterval->i;
                $UserRequest->travel_time = $MintuesTime;
                $UserRequest->save();
                $UserRequest->with('user')->findOrFail($id);
                $UserRequest->invoice = $this->invoice($id);

                (new SendPushNotification)->Dropped($UserRequest);

                //  Helper::site_sendmail($UserRequest);
            }


            // Send Push Notification to User

            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to update, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($id,  true), 'ip' => "At Trip destroy".__LINE__]);
        $UserRequest = UserRequests::find($id);

        try {
            $this->assign_next_provider($UserRequest->id);
            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }
    public function nextpro(Request $request)
    {
      
        
        try{

            $UserRequest = UserRequests::findOrFail($request->id);
            $this->assign_next_provider($UserRequest->id);
            
            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }


    }
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function assign_destroy($id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($id,  true), 'ip' => "At Trip assign_destroy".__LINE__]);
        $UserRequest = UserRequests::find($id);
        try {
            UserRequests::where('id', $UserRequest->id)->update(['status' => 'CANCELLED']);
            // No longer need request specific rows from RequestMeta
            RequestFilter::where('request_id', $UserRequest->id)->delete();
            //  request push to user provider not available
            (new SendPushNotification)->ProviderNotAvailable($UserRequest->user_id);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function assign_next_provider($request_id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request_id,  true), 'ip' => "At Trip assign_next_provider".__LINE__]);
        try {
            $UserRequest = UserRequests::findOrFail($request_id);
        } catch (ModelNotFoundException $e) {
            // Cancelled between update.
            return false;
        }

        $RequestFilter = RequestFilter::where('provider_id', $UserRequest->current_provider_id)
            ->where('request_id', $UserRequest->id)
            ->delete();

        try {

            $next_provider = RequestFilter::where('request_id', $UserRequest->id)
                ->orderBy('id')
                ->firstOrFail();

            $UserRequest->current_provider_id = $next_provider->provider_id;
            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->save();

            // incoming request push to provider
            (new SendPushNotification)->IncomingRequest($next_provider->provider_id);

        } catch (ModelNotFoundException $e) {

            UserRequests::where('id', $UserRequest->id)->update(['status' => 'CANCELLED']);

            // No longer need request specific rows from RequestMeta
            RequestFilter::where('request_id', $UserRequest->id)->delete();

            //  request push to user provider not available
            (new SendPushNotification)->ProviderNotAvailable($UserRequest->user_id);
        }
    }

    public function invoice($request_id)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request_id,  true), 'ip' => "At Trip invoice".__LINE__]);
        try {
            $UserRequest = UserRequests::findOrFail($request_id);
            $tax_percentage = Setting::get('tax_percentage', 10);
            $commission_percentage = Setting::get('commission_percentage', 10);
            $provider_commission_percentage = Setting::get('commission_percentage', 10);
            $service_type = ServiceType::findOrFail($UserRequest->service_type_id);
//            \DB::table("temp_table")->insert(["text" => print_r($UserRequest,  true), 'ip' => "At Trip invoice".__LINE__]);
//            \DB::table("temp_table")->insert(["text" => print_r($tax_percentage,  true), 'ip' => "At Trip invoice".__LINE__]);
//            \DB::table("temp_table")->insert(["text" => print_r($commission_percentage,  true), 'ip' => "At Trip invoice".__LINE__]);
//            \DB::table("temp_table")->insert(["text" => print_r($provider_commission_percentage,  true), 'ip' => "At Trip invoice".__LINE__]);
//            \DB::table("temp_table")->insert(["text" => print_r($service_type,  true), 'ip' => "At Trip invoice".__LINE__]);
            $kilometer = $UserRequest->distance;
            $Fixed = $service_type->fixed;
            $base_distance = $service_type->distance;
//            \DB::table("temp_table")->insert(["text" => print_r($kilometer,  true), 'ip' => "At Trip invoice".__LINE__]);
//            \DB::table("temp_table")->insert(["text" => print_r($Fixed,  true), 'ip' => "At Trip invoice".__LINE__]);
//            \DB::table("temp_table")->insert(["text" => print_r($base_distance,  true), 'ip' => "At Trip invoice".__LINE__]);

            $Distance = 0;
            $minutes = 0;
            $Discount = 0; // Promo Code discounts should be added here.
            $Wallet = 0;
            $Surge = 0;
            $ProviderCommission = 0;
            $ProviderPay = 0;

            $start_time = strtotime($UserRequest->started_at);
            $end_time = strtotime($UserRequest->finished_at);
            $timeLapse = (($end_time - $start_time) / 60);
            $minutes = $timeLapse;

            // $Fixed = $Fixed +$base_distance;

            if ($kilometer <= $base_distance) {
                $kilometer = $base_distance;
            }

            if ($service_type->calculator == 'MIN') {
                $Distance = $service_type->minute * $minutes;
            } else if ($service_type->calculator == 'HOUR') {
                $Distance = $service_type->minute * 60;
            } else if ($service_type->calculator == 'DISTANCE') {
                $Distance = ($kilometer * $service_type->price);
            } else if ($service_type->calculator == 'DISTANCEMIN') {
                $Distance = ($kilometer * $service_type->price) + ($service_type->minute * $minutes);
            } else if ($service_type->calculator == 'DISTANCEHOUR') {
                $Distance = ($kilometer * $service_type->price) + ($service_type->minute * $minutes * 60);
            } else {
                $Distance = ($kilometer * $service_type->price);
            }

            $Tax = ($Distance + $Fixed) * ($tax_percentage / 100);

            $Commision = ($Distance + $Fixed + $Tax) * ($commission_percentage / 100);
            $ProviderCommission = ($Distance + $Fixed + $Tax) * ($provider_commission_percentage / 100);
            $ProviderPay = ($Distance + $Fixed + $Tax) - $ProviderCommission;


            if ($PromocodeUsage = PromocodeUsage::where('user_id', $UserRequest->user_id)->where('status', 'ADDED')->first()) {
                if ($Promocode = Promocode::find($PromocodeUsage->promocode_id)) {
                    $Discount = $Promocode->discount;
                    $PromocodeUsage->status = 'USED';
                    $PromocodeUsage->save();

//                    PromocodePassbook::where('user_id',$UserRequest->user_id)->where('promocode_id', $PromocodeUsage->promocode_id)->update([
//                        'status' => 'USED'
//                    ]);

                    PromocodePassbook::create([
                        'user_id' => Auth::user()->id,
                        'status' => 'USED',
                        'promocode_id' => $PromocodeUsage->promocode_id
                    ]);
                }

                if ($PromocodeUsage->promocode->discount_type == 'amount') {
                    $provider = Provider::findOrFail(Auth::user()->id);
                    \DB::table('providers')->where('id', $provider->id)->update([
                        'wallet_balance' => $provider->wallet_balance + $Discount,
                    ]);
                    \DB::table('provider_discount')->insert(
                        [
                            'provider_id' => Auth::user()->id,
                            'promocode_id' => $PromocodeUsage->promocode->id,
                            'discount' => $Discount,
                            'booking_id' => $UserRequest->booking_id,
                        ]
                    );
                    $Total = $Fixed + $Distance + $Tax - $Discount;


                } else {
                    $Total = ($Fixed + $Distance + $Tax) - (($Fixed + $Distance + $Tax) * ($Discount / 100));
                    $Discount = (($Fixed + $Distance + $Tax) * ($Discount / 100));
                    $provider = Provider::findOrFail(Auth::user()->id);
                    \DB::table('providers')->where('id', $provider->id)->update([
                        'wallet_balance' => $provider->wallet_balance + $Discount,
                    ]);
                    \DB::table('provider_discount')->insert(
                        [
                            'provider_id' => Auth::user()->id,
                            'promocode_id' => $PromocodeUsage->promocode->id,
                            'discount' => $Discount,
                            'booking_id' => $UserRequest->booking_id,
                        ]
                    );
                }


            } else {
                $Total = $Fixed + $Distance + $Tax;
            }


            if ($UserRequest->surge) {
                $Surge = (Setting::get('surge_percentage') / 100) * $Total;
                $Total += $Surge;
            }

            if ($Total < 0) {
                $Total = 0.00; // prevent from negative value
            }

            $Payment = new UserRequestPayment;
            $Payment->request_id = $UserRequest->id;

            /*
            * Reported by Jeya, We are adding the surge price with Base price of Service Type.
            */
            $Payment->fixed = $Fixed + $Surge;
            $Payment->distance = $Distance;
            $Payment->commision = $Commision;
            $Payment->surge = $Surge;
            $Payment->total = $Total;
            $Payment->provider_commission = $ProviderCommission;
            $Payment->provider_pay = $ProviderPay;
            if ($Discount != 0 && $PromocodeUsage) {
                $Payment->promocode_id = $PromocodeUsage->promocode_id;
            }
            $Payment->discount = $Discount;

            if ($Discount == ($Fixed + $Distance + $Tax)) {
                $UserRequest->paid = 1;
            }

            if ($UserRequest->use_wallet == 1 && $Total > 0) {

                $User = User::find($UserRequest->user_id);

                $Wallet = $User->wallet_balance;

                if ($Wallet != 0) {

                    if ($Total > $Wallet) {

                        $Payment->wallet = $Wallet;
                        $Payable = $Total - $Wallet;
                        User::where('id', $UserRequest->user_id)->update(['wallet_balance' => 0]);
                        $Payment->payable = abs($Payable);

                        WalletPassbook::create([
                            'user_id' => $UserRequest->user_id,
                            'amount' => $Wallet,
                            'status' => 'DEBITED',
                            'via' => 'TRIP',
                        ]);

                        // charged wallet money push
                        (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id, currency($Wallet));

                    } else {

                        $Payment->payable = 0;
                        $WalletBalance = $Wallet - $Total;
                        User::where('id', $UserRequest->user_id)->update(['wallet_balance' => $WalletBalance]);
                        $Payment->wallet = $Total;

                        $Payment->payment_id = 'WALLET';
                        $Payment->payment_mode = $UserRequest->payment_mode;

                        $UserRequest->paid = 1;
                        $UserRequest->status = 'COMPLETED';
                        $UserRequest->save();

                        WalletPassbook::create([
                            'user_id' => $UserRequest->user_id,
                            'amount' => $Total,
                            'status' => 'DEBITED',
                            'via' => 'TRIP',
                        ]);

                        // charged wallet money push
                        (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id, currency($Total));
                    }

                }

            } else {
                $Payment->total = abs($Total);
                $Payment->payable = abs($Total);

            }

            $Payment->tax = $Tax;
            $Payment->save();

            return $Payment;

        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function history_details(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip history_details".__LINE__]);
        $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id',
        ]);

        if ($request->ajax()) {

            $Jobs = UserRequests::where('id', $request->request_id)
                ->where('provider_id', Auth::user()->id)
                ->with('payment', 'service_type', 'user', 'rating')
                ->get();
            if (!empty($Jobs)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?" .
                        "autoscale=1" .
                        "&size=320x130" .
                        "&maptype=terrain" .
                        "&format=png" .
                        "&visual_refresh=true" .
                        "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude .
                        "&markers=icon:" . $map_icon . "%7C" . $value->d_latitude . "," . $value->d_longitude .
                        "&path=color:0x000000|weight:3|enc:" . $value->route_key .
                        "&key=" . Setting::get('map_key');
                }
            }

            return $Jobs;
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips()
    {
//        \DB::table("temp_table")->insert(["text" => print_r("h",  true), 'ip' => "At Trip upcoming_trips".__LINE__]);
        try {
            $UserRequests = UserRequests::ProviderUpcomingRequest(Auth::user()->id)->get();
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
                        "&path=color:0x000000|weight:3|enc:" . $value->route_key .
                        "&key=" . Setting::get('map_key');
                }
            }
            return $UserRequests;
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_details(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip upcoming_details".__LINE__]);
        $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id',
        ]);

        if ($request->ajax()) {

            $Jobs = UserRequests::where('id', $request->request_id)
                ->where('provider_id', Auth::user()->id)
                ->with('service_type', 'user')
                ->get();
            if (!empty($Jobs)) {
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?" .
                        "autoscale=1" .
                        "&size=320x130" .
                        "&maptype=terrain" .
                        "&format=png" .
                        "&visual_refresh=true" .
                        "&markers=icon:" . $map_icon . "%7C" . $value->s_latitude . "," . $value->s_longitude .
                        "&markers=icon:" . $map_icon . "%7C" . $value->d_latitude . "," . $value->d_longitude .
                        "&path=color:0x000000|weight:3|enc:" . $value->route_key .
                        "&key=" . Setting::get('map_key');
                }
            }

            return $Jobs;
        }

    }

    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip summary".__LINE__]);
        try {
            if ($request->ajax()) {

                $wallet_history = Auth::user();
                $rides = UserRequests::where('provider_id', Auth::user()->id)->count();
                $revenue = UserRequestPayment::whereHas('request', function ($query) use ($request) {
                    $query->where('provider_id', Auth::user()->id);
                })
                    ->sum('total');
                $providerpay = UserRequestPayment::whereHas('request', function ($query) use ($request) {
                    $query->where('provider_id', Auth::user()->id);
                })
                    ->sum('provider_pay');
                $cashpayment = UserRequestPayment::whereHas('request', function ($query) use ($request) {
                    $query->where('provider_id', Auth::user()->id);
                })
                    ->sum('payable');
                $sercharge = UserRequestPayment::whereHas('request', function ($query) use ($request) {
                    $query->where('provider_id', Auth::user()->id);
                })
                    ->sum('provider_commission');
                $commission_percentage = Setting::get('commission_percentage', 10);
                $sercharge = round($sercharge, 2);
                $cancel_rides = UserRequests::where('status', 'CANCELLED')->where('provider_id', Auth::user()->id)->count();
                $acp = $providerpay - $cashpayment;
                $wdp = $cashpayment - $providerpay;

                if ($acp > 0) {
                    $withdraw = $acp;
                } else {
                    $withdraw = '0.00';
                }
                if ($wdp > 0) {
                    $amountcopay = $wdp;
                } else {
                    $amountcopay = '0.00';
                }
                return response()->json([
                    'rides' => $rides,
                    'revenue' => round($revenue, 2),
                    'serch' => $sercharge,
                    'cashpayment' => round($cashpayment, 2),
                    'amountcopay' => round($amountcopay, 2),
                    'withdraw' => round($withdraw, 2),
                    'cancel_rides' => $cancel_rides,
                    'wallet_balance' => $wallet_history->wallet_balance,
                ]);
            }

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }

    }


    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request)
    {
//        \DB::table("temp_table")->insert(["text" => print_r($request->all(),  true), 'ip' => "At Trip help_details".__LINE__]);
        try {

            if ($request->ajax()) {
                return response()->json([
                    'contact_number' => Setting::get('contact_number', ''),
                    'contact_email' => Setting::get('contact_email', '')
                ]);
            }

        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
        }
    }

}
