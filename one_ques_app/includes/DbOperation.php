<?php
class DbOperation
{
    //Database connection link
    private $con;

    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';

        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();

        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }

    /*
    * The create operation
    * When this method is called a new record is created in the database
    */
    function createQuestion($user_ref_id,$content, $time, $doctor_id, $doctor_answer
	    , $user_answer){

	    $session_id = md5(uniqid($user_ref_id, true));

        $stmt = $this->con->prepare("INSERT INTO user_one_ques_table (user_ref_id, content, time, doctor_ref_id, session_id,doctor_answer, user_answer) VALUES (?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param("sssssss", $user_ref_id,$content, $time, $doctor_id, $session_id,$doctor_answer
            , $user_answer );

	if($stmt->execute()){
		if($user_answer==1){
		$UserStmt = $this->con->prepare("UPDATE users SET is_asked_question = 1 WHERE national_code = $user_ref_id");
		$UserStmt->execute();
		return true;
		}else{
			return true;
		}
        }
        return false;
    }


//    function uploadImageDoc($doc_id, $media_name){
//
////        $path = "uploads/$media_name.png";
//
//        $type = "png";
//
//        $imgPath = "http://185.239.106.95/mp_app/v1/uploads/$media_name";
//
//
//
//        $stmt = $this->con->prepare("INSERT INTO documents_media (doc_id, doc_url, doc_name, doc_type) VALUES (?, ?, ?, ?)");
//        $stmt->bind_param("ssss", $doc_id, $imgPath, $media_name, $type);
//
//
//        if($stmt->execute()){
//            return true;
//        }
//        return false;
//    }

    /*
    * The read operation
    * When this method is called it is returning all the existing record of the database
    */
    function getQuestions($user_ref_id,$doctor_ref_id){
	    $stmt = $this->con->prepare("SELECT question_id,content, time,session_id, doctor_answer, user_answer FROM user_one_ques_table WHERE user_ref_id = '$user_ref_id' AND 
		    doctor_ref_id = '$doctor_ref_id'");
        $content = null;
        $time = null;
        $doctor_answer = null;
	$user_answer = null;
	$question_id = null;
	$session_id = null;




        $stmt->execute();
        $stmt->bind_result($question_id,$content, $time, $session_id,$doctor_answer,$user_answer);
        $docs = array();
	$stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();
		
	    $doc['question_id'] = $question_id;
            $doc['content'] = $content;
	    $doc['time'] = $time;
	    $doc['session_id'] = $session_id;
            $doc['doctor_answer'] = $doctor_answer;
	    $doc['user_answer'] = $user_answer;

            array_push($docs, $doc);
        }

        return $docs;
    }


    function getDoctorQuestion($doctor_id){
        $stmt = $this->con->prepare("SELECT question_id,content, time, doctor_answer, user_answer,user_ref_id FROM user_one_ques_table WHERE doctor_ref_id = $doctor_id");
        $content = null;
        $time = null;
        $doctor_answer = null;
        $user_answer = null;
	$question_id = null;
	$user_id = null;



        $stmt->execute();
        $stmt->bind_result($question_id,$content, $time, $doctor_answer, $user_answer, $user_id);
        $docs = array();
        $stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

            $doc['question_id'] = $question_id;
            $doc['content'] = $content;
	    $doc['time'] = $time;
            $doc['doctor_answer'] = $doctor_answer;
	    $doc['user_answer'] = $user_answer;
	    $doc['user_ref_id'] = $user_id;

            array_push($docs, $doc);
        }

        return $docs;
    }

    function getParticipents($doctor_nt_id,$user_nt_id,$ask_type){

	    if($ask_type=="doc"){
		    $stmt = $this->con->prepare("SELECT user_ref_id FROM user_one_ques_table WHERE doctor_ref_id = $doctor_nt_id");
	    }else{
	    	$stmt = $this->con->prepare("SELECT doctor_ref_id FROM user_one_ques_table WHERE user_ref_id = $user_nt_id");
	    }
	    $user_id = null;
	    $doctor_id = null;
	    $name = null;
	    $lastName = null;
	    $birthDate = null;
	    $profile_pic = null;
	    $specialty = null;
	    $stmt->execute();
	    if($ask_type == "doc"){
		    $stmt->bind_result($user_id);
	    }else{
	    	$stmt->bind_result($doctor_id);
	    }
	 //   $stmt->store_result();
	    $users = array();
	    $uniq_users = array();
	    $docs = array();
	    $doc = array();
	    while($stmt->fetch()){
		    if($ask_type=="doc"){
			    array_push($users,$user_id);
		    }else{
		    	array_push($users,$doctor_id);
		    }
	    }

	    $uniq_users = array_unique($users);

	    foreach($uniq_users as  $value){
		    if($ask_type=="doc"){
	    $getUserStmt = $this->con->prepare("SELECT name,lastName,birthDate FROM users WHERE national_code = $value");
                    $getUserStmt->execute();
                    $getUserStmt->bind_result($name,$lastName,$birthDate);
                   // $getUserStmt->store_result();
                    while($getUserStmt->fetch()){

                            $doc['name'] = $name;
                            $doc['lastName'] = $lastName;
			    $doc['birthDate'] = $birthDate;
			    $doc['national_code'] = $value;

                            array_push($docs,$doc);
		    }
		    }else{
		    	$getUserStmt = $this->con->prepare("SELECT name,lastName,specialty,profile_pic FROM doctors_table WHERE national_code = $value");
                    $getUserStmt->execute();
                    $getUserStmt->bind_result($name,$lastName,$specialty,$profile_pic);
                   // $getUserStmt->store_result();
                    while($getUserStmt->fetch()){

                            $doc['name'] = $name;
                            $doc['lastName'] = $lastName;
			    $doc['national_code'] = $value;
			    $doc['specialty'] = $specialty;
			    $doc['profile_pic'] = $profile_pic;

                            array_push($docs,$doc);
                    }
		    }
		}	    
	    
    return $docs;
    
    }

    function getUserFcm($user_nt){
        $stmt = $this->con->prepare("SELECT notif_token FROM users WHERE national_code = $user_nt");
        
        

        if($stmt->execute()){
        $stmt->bind_result($notif_token);
        $docs = array();
        $stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

            $doc['notif_token'] = $notif_token;

            array_push($docs, $doc);
        }

        return $docs;
	}else{
	return false;
	}
    }

    function getDocFcm($doc_nt){
        $stmt = $this->con->prepare("SELECT notif_token FROM doctors_table WHERE national_code = $doc_nt");


        if($stmt->execute()){
        $stmt->bind_result($notif_token);
        $docs = array();
        $stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

            $doc['notif_token'] = $notif_token;
        
            array_push($docs, $doc);
        }
    
        return $docs;
        }else{
        return false;
        }
    }

}
