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
function createBrief($doctor_ref_id,$content, $offline_user_ref_id, $last_edited_time){

        $stmt = $this->con->prepare("INSERT INTO visit_brief_table (doctor_ref_id, content, offline_user_ref_id, last_edited_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $doctor_ref_id,$content, $offline_user_ref_id, $last_edited_time);
    
        if($stmt->execute()){
            return true; 
           // return mysqli_insert_id($this->con);
        }
        return false;
    }


    function createUser($from_doctor_id,$user_name,$user_last_name, $time, $user_ntcode,$content){

        $stmt = $this->con->prepare("INSERT INTO offline_users_table (from_doctor_id,user_name,user_last_name,user_ntcode,time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $from_doctor_id,$user_name, $user_last_name, $user_ntcode, $time);

	if($stmt->execute()){
		if($content!=null){
			

                //creating a new record in the database

                //creating a new record in the database
                $result = $this->createBrief(
                    $from_doctor_id,
                    $content,
                    mysqli_insert_id($this->con),
                    $time
                );
			return $result;		
		}
		
        }else{
		return false;
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
    function getUsers($doctor_id){
         $stmt = $this->con->prepare("SELECT * FROM offline_users_table WHERE from_doctor_id = $doctor_id");
        $stmt->execute();
        $docs = array();
        $docs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $docs;
    }
       
}
