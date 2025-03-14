<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;
use Exception;
use Setting;
use Storage;

use App\Provider;
use App\UserRequestPayment;
use App\UserRequests;
use App\Helpers\Helper;
use App\ProviderTransaction;

class ProviderResource extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store', 'update', 'destroy', 'disapprove']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $AllProviders = Provider::with('service', 'accepted', 'cancelled')
            ->orderBy('id', 'DESC');

        if (request()->has('fleet')) {
            $providers = $AllProviders->where('fleet', $request->fleet)->get();
        } else {
            $providers = $AllProviders->get();
        }

        return view('admin.providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.providers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:providers,email|email|max:255',
            'mobile' => 'digits_between:6,13',
            'avatar' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
            'password' => 'required|min:6|confirmed',
        ]);

        try {

            $provider = $request->all();

            $provider['password'] = bcrypt($request->password);
            if ($request->hasFile('avatar')) {
                $provider['avatar'] = $request->avatar->store('provider/profile');
            }

            $provider = Provider::create($provider);

            return back()->with('flash_success', 'Provider Details Saved Successfully');

        } catch (Exception $e) {
            return back()->with('flash_error', 'Provider Not Found');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        try {
            $provider = Provider::findOrFail($id);
            return view('admin.providers.provider-details', compact('provider'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    public function deduction($id)
    {
        try {
            $deductions = DB::table('providers')
                ->join('user_requests', 'providers.id', '=', 'user_requests.current_provider_id')
                ->join('user_request_payments', 'user_requests.id', '=', 'user_request_payments.request_id')
                ->select('providers.*', 'user_request_payments.*', 'user_requests.booking_id')->where('user_requests.current_provider_id', $id)
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

    public function payments($id)
    {


        $discounts = $this->discount($id);
        $deposits = $this->deposit($id);
        $deductions = $this->deduction($id);
        $payments = array();
        $i = 0;
        foreach ($discounts as $discount) {
            $payments[$i]['amount'] = $discount->discount;
            $payments[$i]['type'] = "discount";
            $payments[$i]['booking_id'] = $discount->booking_id;
            $payments[$i]['created_at'] = $discount->created_at;
            $payments[$i]['ride'] = 0;
            $i++;
        }


        foreach ($deposits as $deposit) {
            $payments[$i]['amount'] = $deposit->amount;
            $payments[$i]['type'] = "deposit";
            $payments[$i]['created_at'] = $deposit->created_at;
            $payments[$i]['ride'] = 0;
            $i++;
        }

        foreach ($deductions as $deduction) {

            $payments[$i]['amount'] = $deduction->provider_commission;
            $payments[$i]['type'] = "deduction";
            $payments[$i]['booking_id'] = $deduction->booking_id;
            $payments[$i]['created_at'] = $deduction->created_at;
            $payments[$i]['ride'] = $deduction->request_id;


            $i++;
        }

        usort($payments, function ($a, $b) {
            if ($a['created_at'] == $b['created_at']) {
                return 0;
            }
            return ($a['created_at'] < $b['created_at']) ? 1 : -1;
        });

//        echo "<pre>";print_r($payments);die;
        return view('admin.providers.payments', compact('payments'));
    }

    public function deposit($id)
    {
        try {


            $deposits = DB::table('providers')
                ->join('provider_transactions', 'providers.id', '=', 'provider_transactions.provider_id')
                ->select('providers.*', 'provider_transactions.*')->where('provider_id', $id)->get();
            return $deposits;
        } catch (ModelNotFoundException $e) {
            return $e;
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $provider = Provider::findOrFail($id);
            return view('admin.providers.edit', compact('provider'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    public function addToWallet(Request $request)
    {

        $this->validate($request, [
            'id' => 'required',
            'amount' => 'required',
        ]);

        if (!is_numeric($request->amount))
            return redirect()->route('admin.provider.index')->with('flash_error', 'Amount can only be numeric');

        $id = $request->id;
        $provider = Provider::findOrFail($id);
        $total_Wallet_Balance = $provider->wallet_balance + $request->amount;
        $provider->wallet_balance = $total_Wallet_Balance;
        if ($provider->save()) {
            $transaction = new ProviderTransaction();
            $transaction->provider_id = $id;
            $transaction->amount = $request->amount;
            if ($transaction->save())
                return redirect()->route('admin.provider.index')->with('flash_success', 'Provider Wallet Updated Successfully');
            return redirect()->route('admin.provider.index')->with('flash_error', 'Provider balance could not be updated');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile' => 'required',
//            'wallet_balance' => 'required',
            'avatar' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try {

            $provider = Provider::findOrFail($id);

            if ($request->hasFile('avatar')) {
                if ($provider->avatar) {
                    Storage::delete($provider->avatar);
                }
                $provider->avatar = 'app/public/' . $request->avatar->store('provider/profile');
            }
            $provider->first_name = $request->first_name;
            $provider->last_name = $request->last_name;
            $provider->mobile = $request->mobile;
//            $provider->wallet_balance = $request->wallet_balance;
            $provider->save();

            return redirect()->route('admin.provider.index')->with('flash_success', 'Provider Updated Successfully');
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', 'Provider Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        try {
            Provider::find($id)->delete();
//           Provider::where('id',$id)->delete();

            return back()->with('message', 'Provider deleted successfully');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Provider Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        try {
            $Provider = Provider::findOrFail($id);
            if ($Provider->service) {
                $Provider->update(['status' => 'approved']);
                return back()->with('flash_success', "Provider Approved");
            } else {
                return redirect()->route('admin.provider.document.index', $id)->with('flash_error', "Provider has not been assigned a service type!");
            }
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', "Something went wrong! Please try again later.");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function disapprove($id)
    {

        Provider::where('id', $id)->update(['status' => 'banned']);
        return back()->with('flash_success', "Provider Disapproved");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function request($id)
    {

        try {

            $requests = UserRequests::where('user_requests.provider_id', $id)
                ->RequestHistory()
                ->get();

            return view('admin.request.index', compact('requests'));
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    /**
     * account statements.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function statement($id)
    {

        try {

            $requests = UserRequests::where('provider_id', $id)
                ->where('status', 'COMPLETED')
                ->with('payment')
                ->get();

            $rides = UserRequests::where('provider_id', $id)->with('payment')->orderBy('id', 'desc')->paginate(10);
            $cancel_rides = UserRequests::where('status', 'CANCELLED')->where('provider_id', $id)->count();
            $Provider = Provider::find($id);
            $revenue = UserRequestPayment::whereHas('request', function ($query) use ($id) {
                $query->where('provider_id', $id);
            })->select(\DB::raw(
                'SUM(ROUND(total)) as overall, SUM(ROUND(provider_commission)) as commission, SUM(ROUND(tax)) as tax'
            ))->get();


            $Joined = $Provider->created_at ? '- Joined ' . $Provider->created_at->diffForHumans() : '';

            return view('admin.providers.statement', compact('rides', 'cancel_rides', 'revenue'))
                ->with('page', $Provider->first_name . "'s Overall Statement " . $Joined);

        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    public function Accountstatement($id)
    {

        try {

            $requests = UserRequests::where('provider_id', $id)
                ->where('status', 'COMPLETED')
                ->with('payment')
                ->get();

            $rides = UserRequests::where('provider_id', $id)->with('payment')->orderBy('id', 'desc')->paginate(10);
            $cancel_rides = UserRequests::where('status', 'CANCELLED')->where('provider_id', $id)->count();
            $Provider = Provider::find($id);
            $revenue = UserRequestPayment::whereHas('request', function ($query) use ($id) {
                $query->where('provider_id', $id);
            })->select(\DB::raw(
                'SUM(ROUND(total)) as overall, SUM(ROUND(commision)) as commission, SUM(ROUND(tax)) as tax'
            ))->get();


            $Joined = $Provider->created_at ? '- Joined ' . $Provider->created_at->diffForHumans() : '';

            return view('account.providers.statement', compact('rides', 'cancel_rides', 'revenue'))
                ->with('page', $Provider->first_name . "'s Overall Statement " . $Joined);

        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }
}
