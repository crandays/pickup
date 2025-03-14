<?php

namespace App\Http\Controllers\ProviderResources;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;

use Auth;
use Setting;
use Storage;
use Exception;
use Carbon\Carbon;

use App\ProviderProfile;
use App\UserRequests;
use App\ProviderService;
use App\Fleet;
use App\RequestFilter;
use Illuminate\Support\Facades\DB;
class ProfileController extends Controller
{
    /**
     * Create a new user instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('provider.api', ['except' => ['show', 'store', 'available', 'location_edit', 'location_update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            Auth::user()->service = ProviderService::where('provider_id',Auth::user()->id)
                                            ->with('service_type')
                                            ->first();
            Auth::user()->fleet = Fleet::find(Auth::user()->fleet);
            Auth::user()->currency = Setting::get('currency', '$');
            Auth::user()->sos = Setting::get('sos_number', '911');
//            Auth::user()->loyality_point = Provider::select(['loyality_points'])->where('id',Auth::user()->id)->first();

            return Auth::user();

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'mobile' => 'required',
                'avatar' => 'mimes:jpeg,bmp,png',
                'language' => 'max:255',
                'address' => 'max:255',
                'address_secondary' => 'max:255',
                'city' => 'max:255',
                'country' => 'max:255',
                'postal_code' => 'max:255',
            ]);

        try {

            $Provider = Auth::user();

            if($request->has('first_name'))
                $Provider->first_name = $request->first_name;

            if($request->has('last_name'))
                $Provider->last_name = $request->last_name;

            if ($request->has('mobile'))
                $Provider->mobile = $request->mobile;

            if ($request->hasFile('avatar')) {
                Storage::delete($Provider->avatar);
                $Provider->avatar = $request->avatar->store('provider/profile');
            }

            if($request->has('service_type')) {
                if($Provider->service) {
                    if($Provider->service->service_type_id != $request->service_type) {
                        $Provider->status = 'banned';
                    }
                    //$ProviderService = ProviderService::where('provider_id',Auth::user()->id);
                    $Provider->service->service_type_id = $request->service_type;
                    $Provider->service->service_number = $request->service_number;
                    $Provider->service->service_model = $request->service_model;
                    $Provider->service->service_color = $request->service_color;
                    $Provider->service->save();

                } else {
                    ProviderService::create([
                        'provider_id' => $Provider->id,
                        'service_type_id' => $request->service_type,
                        'service_number' => $request->service_number,
                        'service_model' => $request->service_model,
                        'service_color' => $request->service_color,
                    ]);
                    $Provider->status = 'banned';
                }
            }

            if($Provider->profile) {
                $Provider->profile->update([
                        'language' => $request->language ? : $Provider->profile->language,
                        'address' => $request->address ? : $Provider->profile->address,
                        'address_secondary' => $request->address_secondary ? : $Provider->profile->address_secondary,
                        'city' => $request->city ? : $Provider->profile->city,
                        'country' => $request->country ? : $Provider->profile->country,
                        'postal_code' => $request->postal_code ? : $Provider->profile->postal_code,
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'language' => $request->language,
                        'address' => $request->address,
                        'address_secondary' => $request->address_secondary,
                        'city' => $request->city,
                        'country' => $request->country,
                        'postal_code' => $request->postal_code,
                    ]);
            }


            $Provider->save();

            return redirect(route('provider.profile.index'));
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Provider Not Found!'], 404);
        }
    }


	 public function deduction($id)
    {
		try {
            $deductions =DB::table('providers')
            ->join('user_requests', 'providers.id', '=', 'user_requests.current_provider_id') ->join('user_request_payments', 'user_requests.id', '=', 'user_request_payments.request_id')
            ->select('providers.*','user_request_payments.*','user_requests.booking_id')->where('user_requests.current_provider_id',$id)
            ->get();
            return $deductions;
        } catch (ModelNotFoundException $e) {
            return $e;
        }

    }

    public function discount($id)
    {
        try {
            $discount =
            DB::table('provider_discount')
                ->select('provider_discount.*')
                ->where('provider_discount.provider_id', $id)
                ->get();
            return $discount;
        } catch (ModelNotFoundException $e) {
            return $e;
        }

    }

	public function ProviderTransactionHistory()
	{
		$Provider=Auth::user();
		$id=$Provider->id;
		$discounts=$this->discount($id);
		$deposits=$this->deposit($id);
		$deductions=$this->deduction($id);
		$payments=array();
		$i=0;
		foreach ($discounts as $discount) {
            $payments[$i]['amount'] = $discount->discount;
            $payments[$i]['type'] = "discount";
            $payments[$i]['booking_id'] = $discount->booking_id;
            $payments[$i]['created_at'] = $discount->created_at;
            $payments[$i]['ride'] = 0;
            $i++;
        }
		foreach($deposits as $deposit)
		{
			$payments[$i]['amount']=$deposit->amount;
			$payments[$i]['type']="deposit";
			$payments[$i]['created_at']=$deposit->created_at;
			$payments[$i]['ride']=0;
			$i++;
		}

		foreach($deductions as $deduction)
		{
			$payments[$i]['amount']=$deduction->provider_commission;
			$payments[$i]['type']="deduction";
			$payments[$i]['created_at']=$deduction->created_at;
			$payments[$i]['ride']=$deduction->request_id;
			$payments[$i]['booking_id']=$deduction->booking_id;


			$i++;
		}
		usort($payments, function ($a, $b) {
 if ($a['created_at'] == $b['created_at']) {
    return 0;
}
return ($a['created_at'] < $b['created_at']) ? 1 : -1;
});

		return response()->json(['transaction_history' =>$payments]);
	}



	 public function deposit($id)
    {
		try {


            $deposits =DB::table('providers')
            ->join('provider_transactions', 'providers.id', '=', 'provider_transactions.provider_id')
            ->select('providers.*','provider_transactions.*')->where('provider_id',$id)->get();
            return $deposits;
        } catch (ModelNotFoundException $e) {
            return $e;
        }

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('provider.profile.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'mobile' => 'required',
                'avatar' => 'mimes:jpeg,bmp,png',
                'language' => 'max:255',
                'address' => 'max:255',
                'address_secondary' => 'max:255',
                'city' => 'max:255',
                'country' => 'max:255',
                'postal_code' => 'max:255',
            ]);

        try {

            $Provider = Auth::user();

            if($request->has('first_name'))
                $Provider->first_name = $request->first_name;

            if($request->has('last_name'))
                $Provider->last_name = $request->last_name;

            if ($request->has('mobile'))
                $Provider->mobile = $request->mobile;

            if ($request->hasFile('avatar')) {
                Storage::delete($Provider->avatar);
                $Provider->avatar = 'app/public/'. $request->avatar->store('provider/profile');
            }

            if($Provider->profile) {
                $Provider->profile->update([
                        'language' => $request->language ? : $Provider->profile->language,
                        'address' => $request->address ? : $Provider->profile->address,
                        'address_secondary' => $request->address_secondary ? : $Provider->profile->address_secondary,
                        'city' => $request->city ? : $Provider->profile->city,
                        'country' => $request->country ? : $Provider->profile->country,
                        'postal_code' => $request->postal_code ? : $Provider->profile->postal_code,
                    ]);
            } else {
                ProviderProfile::create([
                        'provider_id' => $Provider->id,
                        'language' => $request->language,
                        'address' => $request->address,
                        'address_secondary' => $request->address_secondary,
                        'city' => $request->city,
                        'country' => $request->country,
                        'postal_code' => $request->postal_code,
                    ]);
            }


            $Provider->save();

            return $Provider;
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Provider Not Found!'], 404);
        }
    }

    /**
     * Update latitude and longitude of the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function location(Request $request)
    {
        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

        if($Provider = Auth::user()){

            $Provider->latitude = $request->latitude;
            $Provider->longitude = $request->longitude;
            $Provider->save();

            return response()->json(['message' => 'Location Updated successfully!']);

        } else {
            return response()->json(['error' => 'Provider Not Found!']);
        }
    }

    /**
     * Toggle service availability of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function available(Request $request)
    {
        $this->validate($request, [
                'service_status' => 'required|in:active,offline',
            ]);

        $Provider = Auth::user();

        if($Provider->service) {

            $provider = $Provider->id;
            $OfflineOpenRequest = RequestFilter::with(['request.provider','request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                    $query->where('status','SEARCHING');
                    $query->where('current_provider_id','<>',$provider);
                    $query->orWhereNull('current_provider_id');
                    })->pluck('id');

            if(count($OfflineOpenRequest)>0) {
                RequestFilter::whereIn('id',$OfflineOpenRequest)->delete();
            }

            $Provider->service->update(['status' => $request->service_status]);
        } else {
            return response()->json(['error' => 'You account has not been approved for driving']);
        }

        return $Provider;
    }

    /**
     * Update password of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        $this->validate($request, [
                'password' => 'required|confirmed',
                'password_old' => 'required',
            ]);

        $Provider = Auth::user();

        if(password_verify($request->password_old, $Provider->password))
        {
            $Provider->password = bcrypt($request->password);
            $Provider->save();

            return response()->json(['message' => 'Password changed successfully!']);
        } else {
            return response()->json(['error' => 'Required is new password should not be same as old password'], 422);
        }
    }

    /**
     * Show providers daily target.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function target(Request $request)
    {
        try {

            $Rides = UserRequests::where('provider_id', Auth::user()->id)
                    ->where('status', 'COMPLETED')
                    ->where('created_at', '>=', Carbon::today())
                    ->with('payment', 'service_type')
                    ->get();

            return response()->json([
                    'rides' => $Rides,
                    'rides_count' => $Rides->count(),
                    'target' => Setting::get('daily_target','0')
                ]);

        } catch(Exception $e) {
            return response()->json(['error' => "Something Went Wrong"]);
        }
    }

	public function deductFromWallet(Request $request)
    {
		$this->validate($request, [
                'amount' => 'require',
            ]);
        try {
            $Provider = Auth::user();
			print_r($Provider);die;


        } catch(Exception $e) {
            return response()->json(['error' => "Something Went Wrong"]);
        }
    }

}
