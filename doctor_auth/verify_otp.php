<?php
 
include './include/DbHandler.php';
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$db = new DbHandler();
 
 
$response = array();
$response["error"] = false;
 
if (isset($_POST['otp']) && $_POST['otp'] != '') {
    $otp = $_POST['otp'];
 
 
    $user = $db->activateUser($otp);
 
    if ($user != NULL) {
 
        $response["message"] = "User created successfully!";
        $response["profile"] = $user;
	
	$secret_key = "$B&E)H@McQfTjWnZq4t7w!z%C*F-JaNdRgUkXp2s5u8x/A?D(G+KbPeShVmYq3t6w9y$B&E)H@McQfTjWnZr4u7x!A%C*F-JaNdRgUkXp2s5v8y/B?E(G+KbPeShVmYq3t6w9z$C&F)J@McQfTjWnZr4u7x!A%D*G-KaPdRgUkXp2s5v8y/B?E(H+MbQeThVmYq3t6w9z$C&F)J@NcRfUjXnZr4u7x!A%D*G-KaPdSgVkYp3s5v8y/B?E(H+MbQe";
        $issuer_claim = "MP_Main_Cluster"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 60; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "name" => $user["name"],
		"email" => $user["email"]
        ));
	$jwt = JWT::encode($token, $secret_key);

	$response["jwt"] = $jwt;
		
    } else {
        $response["message"] = "Sorry! Failed to create your account.";
    }
     
     
} else {
    $response["message"] = "Sorry! OTP is missing.";
}
 
 
echo json_encode($response);
?>
