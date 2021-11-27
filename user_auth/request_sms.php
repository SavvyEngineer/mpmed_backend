<?php
 
include './include/DbHandler.php';
include './include/SmsIR_VerificationCode.php';
$db = new DbHandler();
 
 
$response = array();
 
if (isset($_POST['mobile']) && $_POST['mobile'] != '') {
 
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $lastName = $_POST['lastName'];
    $fatherName = $_POST['fatherName'];
    $birthDate = $_POST['birthDate'];
    $bcity = $_POST['bcity'];
    $bstate = $_POST['bstate'];
    $ntCode = $_POST['national_code'];
    $notif_token = $_POST['notif_token'];    
 
    $otp = rand(100000, 999999);
 
    $res = $db->createUser($name, $email, $mobile, $otp, $lastName, $fatherName, $birthDate, $bcity, $bstate, $ntCode, $notif_token);
 
    if ($res == USER_CREATED_SUCCESSFULLY) {
         
        // send sms
        sendSms($mobile, $otp);
         
        $response["error"] = false;
        $response["message"] = "SMS request is initiated! You will be receiving it shortly.";
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Sorry! Error occurred in registration.";
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Mobile number already existed!";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! mobile number is not valid or missing.";
}
 
echo json_encode($response);
 
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
 //       var_dump($VerificationCode);

    } catch (Exeption $e) {
        echo 'Error VerificationCode : '.$e->getMessage();
    }
     
}       
?>
