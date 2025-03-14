<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\ProviderDevice;

use Exception;

use Log;

use Setting;

class SendPushNotification extends Controller

{

	/**

     * New Ride Accepted by a Driver.

     *

     * @return void

     */

    public function RideAccepted($request){

    	return $this->sendPushToUser($request->user_id, trans('api.push.request_accepted'));

    }

    /**

     * Driver Arrived at your location.

     *

     * @return void

     */

    public function user_schedule($user){

        return $this->sendPushToUser($user, trans('api.push.schedule_start'));

    }

    /**

     * New Incoming request

     *

     * @return void

     */

    public function provider_schedule($provider){

        return $this->sendPushToProvider($provider, trans('api.push.schedule_start'));

    }

    /**

     * New Ride Accepted by a Driver.

     *

     * @return void

     */

    public function UserCancellRide($request){
        return $this->sendPushToProvider($request->current_provider_id, trans('api.push.user_cancelled'),'ride_cancel');
    }

    /**

     * New Ride Accepted by a Driver.

     *

     * @return void

     */

    public function ProviderCancellRide($request){

        return $this->sendPushToUser($request->user_id, trans('api.push.provider_cancelled'));

    }

    /**

     * Driver Arrived at your location.

     *

     * @return void

     */

    public function Arrived($request){

        return $this->sendPushToUser($request->user_id, trans('api.push.arrived'));

    }

     /**

     * Driver Arrived at your location.

     *

     * @return void

     */

    public function Dropped($request){

        return $this->sendPushToUser($request->user_id, trans('api.push.dropped').' '.Setting::get('currency').$request->payment->total);
    }

    /**

     * Money added to user wallet.

     *

     * @return void

     */

    public function ProviderNotAvailable($user_id){

        return $this->sendPushToUser($user_id,trans('api.push.provider_not_available'));

    }

    /**

     * New Incoming request

     *

     * @return void

     */

    public function IncomingRequest($provider){

        return $this->sendPushToProvider($provider, trans('api.push.incoming_request'));

    }

    /**

     * Driver Documents verfied.

     *

     * @return void

     */

    public function DocumentsVerfied($provider_id){

        return $this->sendPushToProvider($provider_id, trans('api.push.document_verfied'));

    }

    /**

     * Money added to user wallet.

     *

     * @return void

     */

    public function WalletMoney($user_id, $money){

        return $this->sendPushToUser($user_id, $money.' '.trans('api.push.added_money_to_wallet'));

    }

    /**

     * Money charged from user wallet.

     *

     * @return void

     */

    public function ChargedWalletMoney($user_id, $money){

        return $this->sendPushToUser($user_id, $money.' '.trans('api.push.charged_from_wallet'));

    }

    /**

     * Sending Push to a user Device.

     *

     * @return void

     */

    public function sendPushToUser($user_id, $push_message){

           try{

            $user = User::findOrFail($user_id);

            if($user->device_token !="" ){

                if($user->device_type == 'android'){

                    return $this->sendAndroid1($user->device_token,$push_message);

                }elseif($user->device_type == 'ios'){

                    sendUserIOS($user->device_token,$push_message);

                }

            }

        } catch(Exception $e){

            return $e;

        }

    }

    /**

     * Sending Push to a user Device.

     *

     * @return void

     */

    public function sendPushToProvider($provider_ido, $push_messageo, $option = ""){
        try{

            $provider = ProviderDevice::where('provider_id',$provider_ido)->with('provider')->first();
            if($provider->token != ""){
                if($provider->type == 'ios'){
                    return  sendIOS($provider->token,$push_messageo);
                }elseif($provider->type == 'android'){
                   return $this->sendAndroid2($provider->token,$push_messageo,$option);
                }

            }
        } catch(Exception $e){

            return $e;

        }

    }

    public function sendAndroid1($tokan,$msg)

    {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [

            'title' => 'PickUp',

            "message" => $msg,

            'sound' => true,

        ];

        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [

            //'registration_ids' => $tokenList, //multple token array

            'to'        =>$tokan, //single token

            //'notification' => $notification,

            'data' => $notification

        ];

        $headers = [

            'Authorization: key='.Setting::get('android_user_fcm_key'),

            'Content-Type: application/json'

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$fcmUrl);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));

        $result = curl_exec($ch);

        curl_close($ch);

    }

   public function sendAndroid2($tokan,$msg,$op = "")

    {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [

            'title' => 'PickUp',

            "message" => $msg,

            'sound' => true,
            'ride_cancel' => ($op == "ride_cancel"? "true" : "false"),

        ];

//        \DB::table("temp_table")->insert(["text" => print_r($notification,  true), 'ip' => "At NOTI".__LINE__]);

        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [

            //'registration_ids' => $tokenList, //multple token array

            'to'        =>$tokan, //single token

            //'notification' => $notification,

            'data' => $notification

        ];

        $headers = [

            'Authorization: key='.Setting::get('android_user_driver_key'),

            'Content-Type: application/json'

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$fcmUrl);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));

        $result = curl_exec($ch);

        curl_close($ch);

    }

}
