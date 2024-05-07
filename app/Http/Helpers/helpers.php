<?php

use App\Constants\Status;
use App\Models\Extension;
use App\Models\Verification;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\Auth;



function resolve_complete($order_id)
{

    $curl = curl_init();

    $databody = array('order_id' => "$order_id");

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve-complete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    $status = $var->status ?? null;
    if ($status == true) {
        return 200;
    } else {
        return 500;
    }
}



function send_notification($message)
{

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot6493243183:AAHpZ97GioBOLayRCob64HKqe-pzUOmKntc/sendMessage?chat_id=6743906881',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "649324318",
                'text' => $message,
            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
}






    function send_notification2($message)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot6493243183:AAHpZ97GioBOLayRCob64HKqe-pzUOmKntc/sendMessage?chat_id=6743906881',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "649324318",
                'text' => $message,

            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
    }



function session_resolve($session_id, $ref){

    $curl = curl_init();

    $databody = array(
        'session_id' => "$session_id",
        'ref' => "$ref"
    );


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $message = $var->message ?? null;
    $status = $var->status ?? null;

    $amount = $var->amount ?? null;

    return array([
        'status' => $status,
        'amount' => $amount,
        'message' => $message
    ]);


}




function get_services(){

    $APIKEY = env('KEY');

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPricesVerification",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $services = $var ?? null;

        if ($var == null) {
            $services = null;
        }

        return $services;

}


function create_order($service, $price, $cost, $service_name){


    $verification = Verification::where('user_id', Auth::id())->where('status', 1)->first() ?? null;

    if($verification != null || $verification == 1){
        return 9;
    }

   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$cost",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

   $var = curl_exec($curl);
   curl_close($curl);
   $result = $var ??  null;

    if(strstr($result, "ACCESS_NUMBER") !== false) {

        $parts = explode(":", $result);
        $accessNumber = $parts[0];
        $id = $parts[1];
        $phone = $parts[2];

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $phone;
        $ver->order_id = $id;
        $ver->country = "US";
        $ver->service = $service_name;
        $ver->cost = $price;
        $ver->api_cost = $cost;
        $ver->status = 1;
        $ver->type = 'dailysms';
        $ver->save();
        return 1;

    }elseif($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
        return 0;
    }else{
        return 0;
    }




}

function create_tellbot_order($service, $price, $cost){

    $verification = Verification::where('user_id', Auth::id())->where('status', 1)->first() ?? null;

    if($verification != null || $verification == 1){
        // return 9;
    }

    $states = [
        'CA', 'TX', 'FL', 'NY', 'PA', 'IL', 'OH', 'GA', 'NC', 'MI',
        'NJ', 'VA', 'WA', 'AZ', 'MA', 'TN', 'IN', 'MO', 'MD', 'WI'
    ];

    $randomState = $states[array_rand($states)];

    $APIKEY = env('TELLABOT_KEY');
    $state = $randomState;
    $user = 'oprime';
    $curl = curl_init();

    $markup = 10;
 
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=request&user={$user}&api_key={$APIKEY}&service={$service}&&markup={$markup}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
 
    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ??  null;
    $result = json_decode($result, true);
    $result_d = $result['message'][0];

    // dd($result['mdn']);

 

     if($result['status'] == "ok") {
 
        //  $parts = explode(":", $result);
         $accessNumber = $result_d['mdn'];
         $id = $result_d['id'];
         $phone = $result_d['mdn'];

        //  dd($accessNumber);
 
         $ver = new Verification();
         $ver->user_id = Auth::id();
         $ver->phone = $accessNumber;
         $ver->order_id = $id;
         $ver->country = $state;
         $ver->service = $service;
         $ver->cost = $price;
         $ver->api_cost = $cost;
         $ver->status = 1;
         $ver->type = 'tella';
         $ver->save();
         return 1;
 
     }elseif($result['status'] == "error") {
         return 0;
     }else{
         return 0;
     }
 
 
 
 
 }

function cancel_order($orderID){


   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=setStatus&id=$orderID&status=8",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if(strstr($result, "ACCESS_CANCEL") !== false){

        return 1;

    }else{

        return 0;

    }




}


function cancel_tella_order($orderID){


    $APIKEY = env('TELLABOT_KEY');
    $user = 'oprime';
    $curl = curl_init();
 
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=reject&user={$user}&api_key={$APIKEY}&id={$orderID}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
 
     $var = curl_exec($curl);
     curl_close($curl);
     $result = $var ?? null;
     $result = json_decode($result, true);
 
     if($result['status'] == "ok") {
 
         return 1;
 
     }else{
 
         return 0;
 
     }
 
 
 
 
 }

function check_sms($orderID){



   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getStatus&id=$orderID",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "STATUS_WAIT_CODE") !== false){

        return 2;

    }

    if(strstr($result, "STATUS_CANCEL") !== false){

        return 4;

    }




    if(strstr($result, "STATUS_OK") !== false) {


    $parts = explode(":", $result);
    $text = $parts[0];
    $sms = $parts[1];

        $data['sms'] = $sms;
        $data['full_sms'] = $sms;

        Verification::where('order_id', $orderID)->update([
            'status' => 2,
            'sms' => $sms,
            'full_sms' => $sms,
        ]);

        return 3;

    }


}

function get_tellbot_service() {
    $APIKEY = env('TELLABOT_KEY');
    $user = 'oprime';

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=list_services&user={$user}&api_key={$APIKEY}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $services = $var ?? null;

        if ($var == null) {
            $services = null;
        }

        return $services;
}


function check_tella_sms($mdn){

    $APIKEY = env('TELLABOT_KEY');
    $state = 'NY';
    $user = 'oprime';
    $mdn = $mdn;
    $curl = curl_init();
 
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.tellabot.com/sims/api_command.php?cmd=read_sms&user={$user}&api_key={$APIKEY}&mdn={$mdn}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
 
     $var = curl_exec($curl);
     curl_close($curl);
     $result = $var ?? null;
     $result = json_decode($result, true);
    $result_d = $result['message'][0];
     
     if($result['status'] == "error") {
        return 1;
     }
 
 
     if($result['status'] == "ok") {
 
 
         Verification::where('phone', $mdn)->update([
             'status' => 2,
             'sms' => $result_d['pin'],
             'full_sms' => $result_d['reply'],
         ]);
 
         return 3;
 
     }
 
 
 }