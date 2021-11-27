<?php

include './include/DbHandler.php';
$db = new DbHandler();


$response = array();

if (isset($_POST['mobile']) && $_POST['mobile'] != '') {

    $mobile = $_POST['mobile'];

    $otp = rand(100000, 999999);

    if ($db->isUserExists($mobile)) {

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
        $APIKey = "fb069a57924c6e4eb47afc0";
        $SecretKey = "rivas@c0!";
        $APIURL = "https://ws.sms.ir/";

        // your code

        $SmsIR_VerificationCode = new SmsIR_VerificationCode($APIKey, $SecretKey, $APIURL);
        $VerificationCode = $SmsIR_VerificationCode->verificationCode($otp, $mobile);
        var_dump($VerificationCode);

    } catch (Exeption $e) {
        echo 'Error VerificationCode : '.$e->getMessage();
    }
}

class SmsIR_VerificationCode
{

    /**
     * Gets API Verification Code Url.
     *
     * @return string Indicates the Url
     */
    protected function getAPIVerificationCodeUrl()
    {
        return "api/VerificationCode";
    }

    /**
     * Gets Api Token Url.
     *
     * @return string Indicates the Url
     */
    protected function getApiTokenUrl()
    {
        return "api/Token";
    }

    /**
     * Gets config parameters for sending request.
     *
     * @param string $APIKey    API Key
     * @param string $SecretKey Secret Key
     * @param string $APIURL    API URL
     *
     * @return void
     */
    public function __construct($APIKey, $SecretKey, $APIURL)
    {
        $this->APIKey = $APIKey;
        $this->SecretKey = $SecretKey;
        $this->APIURL = $APIURL;
    }

    /**
     * Verification Code.
     *
     * @param string $Code         Code
     * @param string $MobileNumber Mobile Number
     *

     * @return string Indicates the sent sms result
     */
    public function verificationCode($Code, $MobileNumber)
    {
        $token = $this->_getToken($this->APIKey, $this->SecretKey);
        if ($token != false) {
            $postData = array(
                'Code' => $Code,
                'MobileNumber' => $MobileNumber,
            );

            $url = $this->APIURL.$this->getAPIVerificationCodeUrl();
            $VerificationCode = $this->_execute($postData, $url, $token);
            $object = json_decode($VerificationCode);

            $result = false;
            if (is_object($object)) {
                $result = $object->Message;
            } else {
                $result = false;
            }

        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Gets token key for all web service requests.
     *
     * @return string Indicates the token key
     */
    private function _getToken()

    {
        $postData = array(
            'UserApiKey' => $this->APIKey,
            'SecretKey' => $this->SecretKey,
            'System' => 'php_rest_v_2_0'
        );
        $postString = json_encode($postData);

        $ch = curl_init($this->APIURL.$this->getApiTokenUrl());
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        $resp = false;
        $IsSuccessful = '';
        $TokenKey = '';
        if (is_object($response)) {
            $IsSuccessful = $response->IsSuccessful;
            if ($IsSuccessful == true) {
                $TokenKey = $response->TokenKey;
                $resp = $TokenKey;
            } else {
                $resp = false;
            }
        }
        return $resp;
    }

    /**
     * Executes the main method.
     *
     * @param postData[] $postData array of json data
     * @param string     $url      url
     * @param string     $token    token string
     *
     * @return string Indicates the curl execute result
     */
    private function _execute($postData, $url, $token)
    {
        $postString = json_encode($postData);

        $ch = curl_init($url);

        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-sms-ir-secure-token: '.$token
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
?>
