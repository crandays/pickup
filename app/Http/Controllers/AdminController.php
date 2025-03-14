<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Auth;
use Setting;
use Exception;
use \Carbon\Carbon;
use App\User;
use App\Fleet;
use App\LoyalityPointGiftPurchase;
use App\Admin;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;

class AdminController extends Controller
{
    /** * Create a new controller instance. * * @return void */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /** * Dashboard. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function dashboard()
    {
        try {
            $rides = UserRequests::has('user')->orderBy('id', 'desc')->get();
            $loyality_gift_purchases = LoyalityPointGiftPurchase::orderBy('id', 'desc')->get();
            $cancel_rides = UserRequests::where('status', 'CANCELLED');
            $user_cancelled = $cancel_rides->where('cancelled_by', 'USER')->count();
            $provider_cancelled = $cancel_rides->where('cancelled_by', 'PROVIDER')->count();
            $cancel_rides = $cancel_rides->count();
            $service = ServiceType::count();
            $fleet = Fleet::count();
            $revenue = UserRequestPayment::sum('total');
            $providers = Provider::take(10)->orderBy('rating', 'desc')->get();
            return view('admin.dashboard',
                compact('providers','loyality_gift_purchases' ,'fleet', 'service', 'rides', 'user_cancelled', 'provider_cancelled', 'cancel_rides', 'revenue'));
        } catch (Exception $e) {
            return redirect()->route('admin.user.index')->with('flash_error', 'Something Went Wrong with Dashboard!');
        }
    }

    /** * Heat Map. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function heatmap()
    {
        try {
            $rides = UserRequests::has('user')->orderBy('id', 'desc')->get();
            $providers = Provider::take(10)->orderBy('rating', 'desc')->get();
            return view('admin.heatmap', compact('providers', 'rides'));
        } catch (Exception $e) {
            return redirect()->route('admin.user.index')->with('flash_error', 'Something Went Wrong with Dashboard!');
        }
    }

    /** * Map of all Users and Drivers. * * @return \Illuminate\Http\Response */
    public function map_index()
    {
        return view('admin.map.index');
    }

    /** * Map of all Users and Drivers. * * @return \Illuminate\Http\Response */
    public function map_ajax()
    {
        try {
            $Providers = Provider::where('latitude', '!=', 0)->where('longitude', '!=', 0)->with('service')->get();
            $Users = User::where('latitude', '!=', 0)->where('longitude', '!=', 0)->get();
            for ($i = 0; $i < sizeof($Users); $i++) {
                $Users[$i]->status = 'user';
            }
            $All = $Users->merge($Providers);
            return $All;
        } catch (Exception $e) {
            return [];
        }
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function settings()
    {
        return view('admin.settings.application');
    }

    public function f_settings()
    {
        return view('admin.settings.f_settings');
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function f_settings_store(Request $request)
    {
        if (Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@appoets.com');
        }
        $this->validate($request, ['site_logo' => 'mimes:jpeg,jpg,bmp,png|max:5242880', 'f_img2' => 'mimes:jpeg,jpg,bmp,png|max:5242880',]);
        if ($request->hasFile('f_img2')) {
            $f_img2 = Helper::upload_picture($request->file('f_img2'));
            Setting::set('f_img2', $f_img2);
        }
        if ($request->hasFile('site_logo')) {
            $site_logo = Helper::upload_picture($request->file('site_logo'));
            Setting::set('site_logo', $site_logo);
        }
        Setting::set('site_copyright', $request->site_copyright);
        Setting::set('f_u_url', $request->f_u_url);
        Setting::set('f_p_url', $request->f_p_url);
        Setting::set('site_link', $request->site_link);
        Setting::set('contact_message', $request->contact_message);
        Setting::set('contact_city', $request->contact_city);
        Setting::set('contact_address', $request->contact_address);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('contact_number', $request->contact_number);
        Setting::set('f_text1', $request->f_text1);
        Setting::set('f_text2', $request->f_text2);
        Setting::set('f_text6', $request->f_text6);
        Setting::set('f_text7', $request->f_text7);
        Setting::set('f_text8', $request->f_text8);
        Setting::set('f_text9', $request->f_text9);
        Setting::set('f_text10', $request->f_text10);
        Setting::set('f_text11', $request->f_text11);
        Setting::set('f_text12', $request->f_text12);
        Setting::set('f_text13', $request->f_text13);
        Setting::set('loyality_point_per_ride', $request->loyality_point_per_ride);
        Setting::save();
        return back()->with('flash_success', 'Settings Updated Successfully');
    }

    public function settings_store(Request $request)
    {
        if (Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@appoets.com');
        }
        $this->validate($request, ['map_key' => 'required', 'site_title' => 'required', 'site_icon' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
            'site_logo' => 'mimes:jpeg,jpg,bmp,png|max:5242880', 'f_img2' => 'mimes:jpeg,jpg,bmp,png|max:5242880',]);
        if ($request->hasFile('site_icon')) {
            $site_icon = Helper::upload_picture($request->file('site_icon'));
            Setting::set('site_icon', $site_icon);
        }
        if ($request->hasFile('f_img2')) {
            $f_img2 = Helper::upload_picture($request->file('f_img2'));
            Setting::set('f_img2', $f_img2);
        }
        if ($request->hasFile('site_logo')) {
            $site_logo = Helper::upload_picture($request->file('site_logo'));
            Setting::set('site_logo', $site_logo);
        }
        if ($request->hasFile('site_email_logo')) {
            $site_email_logo = Helper::upload_picture($request->file('site_email_logo'));
            Setting::set('site_email_logo', $site_email_logo);
        }
        Setting::set('site_title', $request->site_title);
        Setting::set('store_link_android', $request->store_link_android);
        Setting::set('store_link_ios', $request->store_link_ios);
        Setting::set('provider_select_timeout', $request->provider_select_timeout);
        Setting::set('provider_search_radius', $request->provider_search_radius);
        Setting::set('sos_number', $request->sos_number);
        Setting::set('contact_number', $request->contact_number);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('site_copyright', $request->site_copyright);
        Setting::set('verification', $request->verification);
        Setting::set('site_copyright', $request->site_copyright);
        Setting::set('f_u_url', $request->f_u_url);
        Setting::set('f_p_url', $request->f_p_url);
        Setting::set('site_link', $request->site_link);
        Setting::set('contact_message', $request->contact_message);
        Setting::set('contact_city', $request->contact_city);
        Setting::set('contact_address', $request->contact_address);
        Setting::set('map_key', $request->map_key);
        Setting::set('android_user_fcm_key', $request->android_user_fcm_key);
        Setting::set('android_user_driver_key', $request->android_user_driver_key);
        Setting::set('tawk_client', $request->tawk_client);
		Setting::set('tawk_driver', $request->tawk_driver);
        Setting::set('appmaintain', $request->has('appmaintain') ? 1 : 0);
        Setting::set('f_f_link', $request->f_f_link);
        Setting::set('f_t_link', $request->f_t_link);
        Setting::set('f_l_link', $request->f_l_link);
        Setting::set('f_i_link', $request->f_i_link);
        Setting::set('f_text1', $request->f_text1);
        Setting::set('f_text2', $request->f_text2);
        Setting::set('f_text6', $request->f_text6);
        Setting::set('f_text7', $request->f_text7);
        Setting::set('f_text8', $request->f_text8);
        Setting::set('f_text9', $request->f_text9);
        Setting::set('f_text10', $request->f_text10);
        Setting::set('f_text11', $request->f_text11);
        Setting::set('f_text12', $request->f_text12);
        Setting::set('f_text13', $request->f_text13);
        Setting::set('loyality_point_per_ride', $request->loyality_point_per_ride);
        Setting::save();
        return back()->with('flash_success', 'Settings Updated Successfully');
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function settings_payment()
    {
        return view('admin.payment.settings');
    }

    /** * Save payment related settings. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function settings_payment_store(Request $request)
    {
        if (Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@appoets.com');
        }
        $this->validate($request,
            ['CARD' => 'in:on', 'CASH' => 'in:on', 'stripe_secret_key' => 'required_if:CARD,on|max:255', 'stripe_publishable_key' => 'required_if:CARD,on|max:255',
                'daily_target' => 'required|integer|min:0', 'weekly_target' => 'required|integer|min:0', 'tax_percentage' => 'required|numeric|min:0|max:100',
                'surge_percentage' => 'required|numeric|min:0|max:100', 'commission_percentage' => 'required|numeric|min:0|max:100',
                'surge_trigger' => 'required|integer|min:0', 'currency' => 'required']);
        Setting::set('CARD', $request->has('CARD') ? 1 : 0);
        Setting::set('CASH', $request->has('CASH') ? 1 : 0);
        Setting::set('stripe_secret_key', $request->stripe_secret_key);
        Setting::set('stripe_publishable_key', $request->stripe_publishable_key);
        Setting::set('daily_target', $request->daily_target);
        Setting::set('weekly_target', $request->weekly_target);
        Setting::set('tax_percentage', $request->tax_percentage);
        Setting::set('surge_percentage', $request->surge_percentage);
        Setting::set('commission_percentage', $request->commission_percentage);
        Setting::set('surge_trigger', $request->surge_trigger);
        Setting::set('currency', $request->currency);
        Setting::set('booking_prefix', $request->booking_prefix);
        Setting::save();
        return back()->with('flash_success', 'Settings Updated Successfully');
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function profile()
    {
        return view('admin.account.profile');
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function profile_update(Request $request)
    {
        if (Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@appoets.com');
        }
        $this->validate($request, ['name' => 'required|max:255', 'email' => 'required', 'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',]);
        try {
            $admin = Auth::guard('admin')->user();
            $admin->name = $request->name;
            $admin->email = $request->email;
            if ($request->hasFile('picture')) {
                $admin->picture = $request->picture->store('admin/profile');
            }
            $admin->save();
            return redirect()->back()->with('flash_success', 'Profile Updated');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function password()
    {
        return view('admin.account.change-password');
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function password_update(Request $request)
    {
        if (Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@appoets.com');
        }
        $this->validate($request, ['old_password' => 'required', 'password' => 'required|min:6|confirmed',]);
        try {
            $Admin = Admin::find(Auth::guard('admin')->user()->id);
            if (password_verify($request->old_password, $Admin->password)) {
                $Admin->password = bcrypt($request->password);
                $Admin->save();
                return redirect()->back()->with('flash_success', 'Password Updated');
            }
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function payment()
    {
        try {
            $payments = UserRequests::where('paid', 1)->has('user')->has('provider')->has('payment')->orderBy('user_requests.created_at', 'desc')->get();
            return view('admin.payment.payment-history', compact('payments'));
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }


    /** * User Rating. * * @return \Illuminate\Http\Response */
    public function user_review()
    {
        try {
            $Reviews = UserRequestRating::where('user_id', '!=', 0)->with('user', 'provider')->get();
            return view('admin.review.user_review', compact('Reviews'));
        } catch (Exception $e) {
            return redirect()->route('admin.setting')->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * Provider Rating. * * @return \Illuminate\Http\Response */
    public function provider_review()
    {
        try {
            $Reviews = UserRequestRating::where('provider_id', '!=', 0)->with('user', 'provider')->get();
            return view('admin.review.provider_review', compact('Reviews'));
        } catch (Exception $e) {
            return redirect()->route('admin.setting')->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * Remove the specified resource from storage. * * @param \App\ProviderService * @return \Illuminate\Http\Response */
    public function destory_provider_service($id)
    {
        try {
            ProviderService::find($id)->delete();
            return back()->with('message', 'Service deleted successfully');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * Testing page for push notifications. * * @return \Illuminate\Http\Response */
    public function push_index()
    {
        $data = PushNotification::app('IOSUser')->to('163e4c0ca9fe084aabeb89372cf3f664790ffc660c8b97260004478aec61212c')->send('Hello World, i`m a push message');
        dd($data);
        $data = PushNotification::app('IOSProvider')->to('a9b9a16c5984afc0ea5b681cc51ada13fc5ce9a8c895d14751de1a2dba7994e7')->send('Hello World, i`m a push message');
        dd($data);
    }

    /** * Testing page for push notifications. * * @return \Illuminate\Http\Response */
    public function push_store(Request $request)
    {
        try {
            ProviderService::find($id)->delete();
            return back()->with('message', 'Service deleted successfully');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * privacy. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function privacy()
    {
        return view('admin.pages.static')->with('title', "Privacy Page")->with('page', "privacy");
    }

    public function terms()
    {
        return view('admin.pages.terms')->with('title', "Terms Page")->with('page', "terms");
    }

    /** * pages. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function pages(Request $request)
    {
        $this->validate($request, ['page' => 'required', 'content' => 'required',]);
        Setting::set($request->page, $request->content);
        Setting::save();
        return back()->with('flash_success', 'Content Updated!');
    }

    /** * account statements. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function statement($type = 'individual')
    {
        try {
            $page = 'Ride Statement';
            if ($type == 'individual') {
                $page = 'Provider Ride Statement';
            } elseif ($type == 'today') {
                $page = 'Today Statement - ' . date('d M Y');
            } elseif ($type == 'monthly') {
                $page = 'This Month Statement - ' . date('F');
            } elseif ($type == 'yearly') {
                $page = 'This Year Statement - ' . date('Y');
            }
            $rides = UserRequests::with('payment')->orderBy('id', 'desc');
            $cancel_rides = UserRequests::where('status', 'CANCELLED');
            $revenue = UserRequestPayment::select(\DB::raw('SUM(ROUND(total)) as overall, SUM(ROUND(commision)) as commission, SUM(ROUND(tax)) as tax'));
            if ($type == 'today') {
                $rides->where('created_at', '>=', Carbon::today());
                $cancel_rides->where('created_at', '>=', Carbon::today());
                $revenue->where('created_at', '>=', Carbon::today());
            } elseif ($type == 'monthly') {
                $rides->where('created_at', '>=', Carbon::now()->month);
                $cancel_rides->where('created_at', '>=', Carbon::now()->month);
                $revenue->where('created_at', '>=', Carbon::now()->month);
            } elseif ($type == 'yearly') {
                $rides->where('created_at', '>=', Carbon::now()->year);
                $cancel_rides->where('created_at', '>=', Carbon::now()->year);
                $revenue->where('created_at', '>=', Carbon::now()->year);
            }
            $rides = $rides->get();
            $cancel_rides = $cancel_rides->count();
            $revenue = $revenue->get();
            return view('admin.providers.statement', compact('rides', 'cancel_rides', 'revenue'))->with('page', $page);
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }


    public function userCancelRides()
    {
        try {
//            $requests = UserRequests::RequestHistory()->get();
            $requests = UserRequests::RequestHistory()->where('cancelled_by','USER')->orderBy('created_at', 'ASC')->get();

            return view('admin.providers.user-cancel-rides', compact('requests'));
        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }

    /** * account statements today. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function statement_today()
    {
        return $this->statement('today');
    }

    /** * account statements monthly. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function statement_monthly()
    {
        return $this->statement('monthly');
    }

    /** * account statements monthly. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function statement_yearly()
    {
        return $this->statement('yearly');
    }

    /** * account statements. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function statement_provider()
    {
        try {
            $Providers = Provider::all();
            foreach ($Providers as $index => $Provider) {
                $Rides = UserRequests::where('provider_id', $Provider->id)->where('status', '<>', 'CANCELLED')->get()->pluck('id');
                $Providers[$index]->rides_count = $Rides->count();
                $Providers[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
                    ->select(\DB::raw('SUM(ROUND(total)) as overall, SUM(ROUND(commision)) as commission, SUM(ROUND(tax)) as tax'))->get();
            }
            return view('admin.providers.provider-statement', compact('Providers'))->with('page', 'Providers Statement');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    /** * Remove the specified resource from storage. * * @param \App\Provider $provider * @return \Illuminate\Http\Response */
    public function translation()
    {
        try {
            return view('admin.translation');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }
}
