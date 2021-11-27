<?php
class DbOperation
{
    //Database connection link
	private $con;
	private $AuthLink;
	

    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
	    require_once dirname(__FILE__) . '/DbConnect.php';
	    require_once dirname(__FILE__) . '../../user_token_authenticator/includes/AuthDbOperation.php';

        //Creating a DbConnect object to connect to the database
	    $db = new DbConnect();
	    $Auth = new AuthDbOperation();

        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
	    $this->con = $db->connect();
	    $this->AuthLink = $Auth;
    }


    /*
    * The create operation
    * When this method is called a new record is created in the database
    */
	function createAccess($doc_nt_code,$user_ntcode , $accessed_doc, $time){
		if($this->AuthLink->isTokenValid())

        $stmt = $this->con->prepare("INSERT INTO access_table (doc_nt_ref, user_ntcode, accessed_doc, time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $doc_nt_code,$user_ntcode,$accessed_doc, $time);

        if($stmt->execute()){
	    $stmt->close();
        $stmtIsSeen = $this->con->prepare("INSERT INTO doc_seen_status (document_id, time,doctor_id) VALUES (?,?,?)");
        $stmtIsSeen->bind_param("sss",$accessed_doc,$time,$doc_nt_code);

	if($stmtIsSeen->execute()){
	return mysqli_insert_id($this->con);	
	}	
	else	{
	return false;
		}
        }
   
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
    function getAccessTable($doc_nt_code){
        $stmt = $this->con->prepare("SELECT access_id,user_ntcode,accessed_doc,time FROM access_table WHERE doc_nt_ref = $doc_nt_code");
        $access_id = null;
        $time = null;
	$accessed_doc = null;
	$user_ntcode = null;



        $stmt->execute();
        $stmt->bind_result($access_id,$user_ntcode,$accessed_doc, $time);
        $docs = array();
	$stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

	    $doc['access_id'] = $access_id;
	    $doc['user_ntcode'] = $user_ntcode;
            $doc['accessed_doc'] = $accessed_doc;
	    $doc['time'] = $time;

            array_push($docs, $doc);
        }
	return $docs;
    }

    function CheckIfRowExists($doc_nt_code,$accessed_doc){
	    $stmt = $this->con->prepare("SELECT * FROM access_table WHERE doc_nt_ref = $doc_nt_code AND accessed_doc = $accessed_doc");
	$stmt->execute();
	    $stmt->store_result();

	    if($stmt->num_rows == 0){
		return false;
	}else{
		return true;
	}
    }

}
