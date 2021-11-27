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
    function createReview($document_id,$content, $time, $doctor_id, $doctor_answer
        , $user_answer){

        $stmt = $this->con->prepare("INSERT INTO doc_review (document_id, content, time, doctor_id, doctor_answer, user_answer) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $document_id,$content, $time, $doctor_id, $doctor_answer
            , $user_answer );

        if($stmt->execute()){
	    
	    return mysqli_insert_id($this->con);
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
    function getDocReview($document_id,$doctor_id){
        $stmt = $this->con->prepare("SELECT review_id,content, time, doctor_answer, user_answer FROM doc_review WHERE document_id = $document_id AND doctor_id = $doctor_id");
        $content = null;
        $time = null;
        $doctor_answer = null;
	$user_answer = null;
	$review_id = null;



        $stmt->execute();
        $stmt->bind_result($review_id,$content, $time, $doctor_answer, $user_answer);
        $docs = array();
	$stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();
		
	    $doc['review_id'] = $review_id;
            $doc['content'] = $content;
            $doc['time'] = $time;
            $doc['doctor_answer'] = $doctor_answer;
            $doc['user_answer'] = $user_answer;

            array_push($docs, $doc);
        }

        return $docs;
    }


    function getDocReviewNtCodes($document_id){
        $stmt = $this->con->prepare("SELECT review_id,content, time, doctor_id, doctor_answer, user_answer FROM doc_review WHERE document_id = $document_id");
        $content = null;
        $time = null;
        $doctor_answer = null;
        $user_answer = null;
	$review_id = null;
	$doctor_id = null;



        $stmt->execute();
        $stmt->bind_result($review_id,$content, $time, $doctor_id, $doctor_answer, $user_answer);
        $docs = array();
        $stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

            $doc['review_id'] = $review_id;
            $doc['content'] = $content;
	    $doc['time'] = $time;
	    $doc['doctor_id'] = $doctor_id;
            $doc['doctor_answer'] = $doctor_answer;
            $doc['user_answer'] = $user_answer;

            array_push($docs, $doc);
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
