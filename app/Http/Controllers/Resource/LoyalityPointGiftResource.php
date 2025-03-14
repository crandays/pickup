<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

use DB;
use Exception;
use Setting;
use Storage;

use App\User;
use App\Provider;
use App\LoyalityPointGift;
use App\LoyalityPointGiftPurchase;
use App\Helpers\Helper;

class LoyalityPointGiftResource extends Controller
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
        $AllLoyality = LoyalityPointGift::orderBy('id', 'DESC');
        $providers = $AllLoyality->get();

        return view('admin.loyality_point_gifts.index', compact('providers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.loyality_point_gifts.create');
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
            'title' => 'required|max:255',
            'description' => 'required|max:2500',
            'image' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try {

            $provider = $request->all();
            $provider['slug'] = Str::slug(strip_tags(\Request::get('title')));
            $provider['price_in_points'] = (int)$request->price_in_points;
            $provider['status'] = (int)$request->status;
            if ($request->hasFile('image')) {
                $provider['image'] = $request->image->store('loyality-point-gifts');
            }

            $provider = LoyalityPointGift::create($provider);

            return back()->with('flash_success', 'loyality point gifts Saved Successfully');

        } catch (Exception $e) {
            return back()->with('flash_error', 'loyality point gifts Not Found'.$e);
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
            $provider = LoyalityPointGift::findOrFail($id);
            return view('admin.loyality_point_gifts.lpg-details', compact('provider'));
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
            $provider = LoyalityPointGift::where('id',$id)->first();
            return view('admin.loyality_point_gifts.edit', compact('provider'));
        } catch (ModelNotFoundException $e) {
            return $e;
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
            'title' => 'required|max:255',
            'description' => 'required|max:2500',
            'image' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try {

            $provider = LoyalityPointGift::findOrFail($id);

            if ($request->hasFile('image')) {
                if ($provider->image) {
                    Storage::delete($provider->image);
                }
                $provider->image =  $request->image->store('loyality-point-gifts');
            }


            $provider->title = $request->title;
            $provider->slug = Str::slug(strip_tags(\Request::get('title')));
            $provider->description = $request->description;
            $provider->price_in_points = (int)$request->price_in_points;
            $provider->status = (int)$request->status;
            $provider->save();

            return redirect()->route('admin.loyality-point-gifts.index')->with('flash_success', 'Loyality point gifts Updated Successfully');
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', 'Loyality point gifts Not Found');
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
            LoyalityPointGift::find($id)->delete();
//           Provider::where('id',$id)->delete();

            return back()->with('message', 'Loyality point gifts deleted successfully');
        } catch (Exception $e) {
            return back()->with('flash_error', 'Loyality point gifts Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function history($id)
    {
        try {
            $requests = LoyalityPointGiftPurchase::where('loyality_point_gift_id', $id)->get();
            return view('admin.loyality_point_gifts.history', compact('requests'));
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }


    public function purchases()
    {
        try {
            $requests = LoyalityPointGiftPurchase::get();
            return view('admin.loyality_point_gifts.purchases', compact('requests'));
        } catch (Exception $e) {
            return back()->with('flash_error', 'Something Went Wrong!');
        }
    }

    public function delivered(Request $request,$id)
    {

        try {

            $lpg_purchase = LoyalityPointGiftPurchase::findOrFail($id);
            $lpg_purchase->status = 2;
            $lpg_purchase->save();

            return back()->with('flash_success', 'Loyality point gifts purchase Updated successfully');
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', 'Loyality point gifts purchase Not Found');
        }
    }

    public function cancel(Request $request,$id)
    {
        try {
            $lpg_purchase = LoyalityPointGiftPurchase::findOrFail($id);

            if($lpg_purchase){

                if($lpg_purchase->buyer == 1){
                    $user = User::where('id', $lpg_purchase->buyer_id)->first();
                    User::where('id', $lpg_purchase->buyer_id)->update([
                        "loyality_points" => $user->loyality_points + $lpg_purchase->points_on_purchase
                    ]);
                }else{

                    $provider = Provider::where('id', $lpg_purchase->buyer_id)->first();
               
                    Provider::where('id', $lpg_purchase->buyer_id)->update([
                        "loyality_points" => $provider->loyality_points + $lpg_purchase->points_on_purchase
                    ]);
                }
            }
            $lpg_purchase->status = 3;
            $lpg_purchase->save();

            return back()->with('flash_success', 'Loyality point gifts purchase Updated successfully');
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', 'Loyality point gifts purchase Not Found');
        }
    }
}
