
<?php

include './include/DbHandler.php';
include './include/SmsIR_VerificationCode.php';
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
    $response["message"] = "Sorry! OTP is missing.";
}

function sendSms($mobile, $otp) {

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
