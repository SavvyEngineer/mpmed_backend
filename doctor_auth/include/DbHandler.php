<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler {

	private $conn;
	

    function __construct() {
	    require_once dirname(__FILE__) . '/DbConnect.php';
	   
        // opening db connection
	    $db = new DbConnect();
	    
	    $this->conn = $db->connect();
	    

    }

    /* ------------- `users` table method ------------------ */

    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $mobile User mobile number
     * @param String $otp user verificaiton code
     */
    public function createUser($md_code,$specialty,$name, $email, $mobile, $otp, $lastName, $fatherName, $birthDate, $wcity, $wstate, $ntCode,$used_md_app,$profile_pic_name,$notif_token) {
        $response = array();



        // First check if user already existed in db
	if (!$this->isUserExists($mobile)) {

		
	    $profile_pic = "https://mpmed.ir/doctor_auth/uploads/$profile_pic_name";

            // Generating API key
            $api_key = $this->generateApiKey();

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO doctors_table(md_code,specialty,name, email, mobile, apikey, lastName, fatherName, birthDate, wcity, wstate, national_code, used_md_app, profile_pic, notif_token, status) values(?,?, ?, ?, ?, ?, ?,?, ?, ? ,?, ?,?,?,?, 0)");
            $stmt->bind_param("sssssssssssssss", $md_code, $specialty,$name, $email, $mobile, $api_key,$lastName,$fatherName,$birthDate,$wcity,$wstate,$ntCode,$used_md_app,$profile_pic, $notif_token);

            $result = $stmt->execute();

            $new_user_id = $stmt->insert_id;


            // Check for successful insertion
            if ($result) {

                $otp_result = $this->createOtp($new_user_id, $otp);
		
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
		//    return USER_CREATE_FAILED;
		   return $stmt->error;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }
	$stmt->close();
        return $response;
    }

    public function createOtp($user_id, $otp) {

        // delete the old otp if exists
        $stmt = $this->conn->prepare("DELETE FROM doctor_sms_codes where user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();


        $stmt = $this->conn->prepare("INSERT INTO doctor_sms_codes(user_id, code, status) values(?, ?, 0)");
        $stmt->bind_param("is", $user_id, $otp);

        $result = $stmt->execute();

        $stmt->close();

        return $result;
    }

    /**
     * Checking for duplicate user by mobile number
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($mobile) {
        $stmt = $this->conn->prepare("SELECT id from doctors_table WHERE mobile = ? and status = 1");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    public function isNationalCodeExists($NationalCode) {
        $stmt = $this->conn->prepare("SELECT * from doctors_table WHERE national_code = ?");

        $stmt->bind_param("s", $NationalCode);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }


    public function GetMobile($NtCode,$otp,$notif_token){

        $mobile = null;
        $stmt = $this->conn->prepare("SELECT mobile, id FROM doctors_table WHERE national_code = ? ");

        $stmt->bind_param("s", $NtCode);

        $stmt->execute();
        // $user = $stmt->get_result()->fetch_assoc();
        $stmt->bind_result($mobile,$id);

        $stmt->store_result();

        while ($stmt->fetch()){

            // activate the user
            // $this->activateUserStatus($id);

            $user = array();
            $user["mobile"] = $mobile;
            $user["id"] = $id;
        }

        $this->createOtp($id,$otp);

        $stmt_token = $this->conn->prepare("UPDATE doctors_table SET notif_token = '$notif_token' WHERE national_code = '$NtCode'");

        $result = $stmt_token->execute();

        $stmt_token->close();

        if($result){
                return $mobile;
                }
    }



    public function activateUser($otp) {
        $stmt = $this->conn->prepare("SELECT u.id, u.md_code, u.specialty, u.name, u.email, u.mobile, u.apikey, u.status, u.created_at, u.lastName, u.fatherName, u.birthDate, u.wcity, u.wstate, u.national_code, u.is_approved, u.used_md_app, u.profile_pic, u.notif_token FROM doctors_table u, doctor_sms_codes WHERE doctor_sms_codes.code = ? AND doctor_sms_codes.user_id = u.id");
        $stmt->bind_param("s", $otp);

        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($id, $md_code, $specialty, $name, $email, $mobile, $apikey, $status, $created_at, $lastName, $fatherName, $birthDate, $wcity, $wstate
                , $national_code, $is_approved,$used_md_app,$profile_pic,$notif_token);

            $stmt->store_result();

            if ($stmt->num_rows > 0) {

                $stmt->fetch();

                // activate the user
                $this->activateUserStatus($id);

                $user = array();
                $user["md_code"] = $md_code;
                $user["specialty"] = $specialty;
                $user["name"] = $name;
                $user["email"] = $email;
                $user["mobile"] = $mobile;
                $user["apikey"] = $apikey;
                $user["status"] = $status;
                $user["created_at"] = $created_at;
                $user["lastName"] = $lastName;
                $user["fatherName"] = $fatherName;
                $user["birthDate"] = $birthDate;
                $user["wcity"] = $wcity;
                $user["wstate"] = $wstate;
                $user["national_code"] = $national_code;
                $user["is_approved"] = $is_approved;
		$user["used_md_app"] = $used_md_app;
		$user["profile_pic"] = $profile_pic;
		$user["notif_token"] = $notif_token;

                $stmt->close();

                return $user;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }

    }

    public function activateUserStatus($user_id){
        $stmt = $this->conn->prepare("UPDATE doctors_table set status = 1 where id = ?");
        $stmt->bind_param("i", $user_id);

        $stmt->execute();

        $stmt = $this->conn->prepare("UPDATE doctor_sms_codes set status = 1 where user_id = ?");
        $stmt->bind_param("i", $user_id);

        $stmt->execute();
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
}
?>
