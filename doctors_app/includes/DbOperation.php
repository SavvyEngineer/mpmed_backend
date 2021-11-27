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
    function createWeekDays($doctor_ref_id,$edited_time, $day_0, $day_1, $day_2,
         $day_3,$day_4,$day_5,$day_6){

	$stmt = $this->con->prepare("INSERT INTO doctor_working_days_table 
		(doctor_ref_id, edited_time, day_0, day_1, day_2, day_3, day_4, day_5, day_6)
	       	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $doctor_ref_id,$edited_time, $day_0, $day_1, $day_2
            , $day_3,$day_4,$day_5,$day_6);

	if($stmt->execute()){
		return true;
		}
        
        return false;
    }

    function updateWorkingHours($doctor_ref_id,$edited_time
	    ,$day_0,$day_1,$day_2,$day_3,$day_4,$day_5,$day_6){
    	

	    $stmt = $this->con->prepare("UPDATE doctor_working_days_table SET edited_time = '$edited_time', day_0 = '$day_0', day_1 = '$day_1'
		   , day_2 = '$day_2', day_3 = '$day_3', day_4 = '$day_4', day_5 = '$day_5'
, day_6 = '$day_6' WHERE doctor_ref_id = '$doctor_ref_id'");
   	    
	    if($stmt->execute()){
	    	return true;
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
    function getWeekDays($doctor_ref_id){
        $stmt = $this->con->prepare("SELECT * FROM doctor_working_days_table WHERE doctor_ref_id = '$doctor_ref_id'");
        $stmt->execute();
        $docs = array();
        $docs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $docs;
    }


}
