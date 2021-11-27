
<?php

include './include/DbHandler.php';
include './include/SmsIR_VerificationCode.php';

// Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }


$db = new DbHandler();


$response = array();
$response["error"] = false;

if (isset($_POST['ntcode']) && $_POST['ntcode'] != '') {
    $NtCode = $_POST['ntcode'];
    $notif_token = $_POST['notif_token'];


    if ($db->isNationalCodeExists($NtCode)) {
        $otp = rand(100000, 999999);
        $mobile = $db->GetMobile($NtCode,$otp,$notif_token);
        $response["mobile"] = $mobile;

//      $otp = rand(100000, 999999);
//      $mobile = $db->GetMobile($NtCode,$otp);
        $response["error"] = false;
        $response["message"] = "Login";
        sendSms($mobile,$otp);


    } else {
        $response["error"] = true;
        $response["message"] = "SignUp";
    }


} else {
    $response["message"] = "Sorry! NtCode is missing.";
}

function sendSms($mobile, $otp) {

//    $otp_prefix = ':';
//
//    //Your message to send, Add URL encoding here.
//    $message = urlencode("Hello! Welcome to AndroidHive. Your OPT is '$otp_prefix $otp'");
//
//    $response_type = 'json';
//
//    //Define route
//    $route = "4";
//
//    //Prepare you post parameters
//    $postData = array(
//        'authkey' => MSG91_AUTH_KEY,
//        'mobiles' => $mobile,
//        'message' => $message,
//        'sender' => MSG91_SENDER_ID,
//        'route' => $route,
//        'response' => $response_type
//    );
//
////API URL
//    $url = "https://control.msg91.com/sendhttp.php";
//
//// init the resource
//    $ch = curl_init();
//    curl_setopt_array($ch, array(
//        CURLOPT_URL => $url,
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_POST => true,
//        CURLOPT_POSTFIELDS => $postData
//        //,CURLOPT_FOLLOWLOCATION => true
//    ));
//
//
//    //Ignore SSL certificate verification
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//
//
//    //get response
//    $output = curl_exec($ch);
//
//    //Print error if any
//    if (curl_errno($ch)) {
//        echo 'error:' . curl_error($ch);
//    }
//
//    curl_close($ch);


    try {
        date_default_timezone_set("Asia/Tehran");

        // your sms.ir panel configuration
        $APIKey = "fc26bc476c79199f5dc6bc4";
        $SecretKey = "MizPeZeSk137713771998!#&&!#&&!((*";
        $APIURL = "https://ws.sms.ir/";

        // your code

        $SmsIR_VerificationCode = new SmsIR_VerificationCode($APIKey, $SecretKey, $APIURL);
        $VerificationCode = $SmsIR_VerificationCode->verificationCode($otp, $mobile);
        

    } catch (Exeption $e) {
        echo 'Error VerificationCode : '.$e->getMessage();
    }
}

echo json_encode($response);
?>
