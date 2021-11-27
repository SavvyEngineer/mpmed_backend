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
    function createDocument($user_id,$name, $last_name, $date, $doctor_name
        , $reason, $lab_name, $exam_type){

        $stmt = $this->con->prepare("INSERT INTO documents (user_id, name, last_name, date, doctor_name, reason
, lab_name, exam_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $user_id, $name, $last_name, $date, $doctor_name,$reason
            ,$lab_name, $exam_type );

        if($stmt->execute()){
	    
	    return mysqli_insert_id($this->con);
        }
        return false;
    }


    function uploadImageDoc($doc_id,$type, $media_name){

//        $path = "uploads/$media_name.png";

       

        $imgPath = "https://mpmed.ir/mp_app/v1/uploads/$media_name";



        $stmt = $this->con->prepare("INSERT INTO documents_media (doc_id, doc_url, doc_name, doc_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $doc_id, $imgPath, $media_name, $type);


        if($stmt->execute()){
            return true;
        }
        return false;
    }

    /*
    * The read operation
    * When this method is called it is returning all the existing record of the database
    */
    function getDoc($ntcode){
        $stmt = $this->con->prepare("SELECT id, user_id, name, last_name, date, doctor_name, reason, lab_name, exam_type, is_reviewed, is_seen, review_doc_ntcode FROM documents WHERE user_id = $ntcode");
        $id = null;
        $user_id = null;
        $name = null;
        $last_name = null;
        $date = null;
        $doctor_name = null;
        $reason = null;
        $lab_name = null;
	$exam_type = null;
	$is_reviewed = null;
	$is_seen = null;
	$review_doc_ntcode = null;
	

        $stmt->execute();
        $stmt->bind_result($id, $user_id, $name, $last_name, $date, $doctor_name,$reason,$lab_name,$exam_type,$is_reviewed,$is_seen,$review_doc_ntcode);
        $docs = array();
        while($stmt->fetch()){
            $doc  = array();

            $doc['id'] = $id;
            $doc['user_id'] = $user_id;
            $doc['name'] = $name;
            $doc['last_name'] = $last_name;
            $doc['date'] = $date;
            $doc['doctor_name'] = $doctor_name;
            $doc['reason'] = $reason;
            $doc['lab_name'] = $lab_name;
	    $doc['exam_type'] = $exam_type;
	    $doc['is_reviewed'] = $is_reviewed;
	    $doc['is_seen'] = $is_seen;
	    $doc['review_doc_ntcode'] = $review_doc_ntcode;

            array_push($docs, $doc);
	}
	$stmt->close();

        return $docs;
    }

    function getDoctorsList(){
    	$stmt = $this->con->prepare("SELECT * FROM doctors_table ");
 	$stmt->execute();
        $docs = array();
        $docs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $docs;	

    }


function getDocByDocId($DocId){
        $stmt = $this->con->prepare("SELECT id, user_id, name, last_name, date, doctor_name, reason, lab_name, exam_type, is_reviewed, is_seen, review_doc_ntcode FROM documents WHERE id = $DocId");
        $id = null;
        $user_id = null;
        $name = null;
        $last_name = null;
        $date = null;
        $doctor_name = null;
        $reason = null;
        $lab_name = null;
	$exam_type = null;
	$is_reviewed = null;
	$is_seen = null;
	$review_doc_ntcode = null;
	

        $stmt->execute();
        $stmt->bind_result($id, $user_id, $name, $last_name, $date, $doctor_name,$reason,$lab_name,$exam_type,$is_reviewed, $is_seen,$review_doc_ntcode);
        $docs = array();
        $stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

            $doc['id'] = $id;
            $doc['user_id'] = $user_id;
            $doc['name'] = $name;
            $doc['last_name'] = $last_name;
            $doc['date'] = $date;
            $doc['doctor_name'] = $doctor_name;
            $doc['reason'] = $reason;
            $doc['lab_name'] = $lab_name;
	    $doc['exam_type'] = $exam_type;
	    $doc['is_reviewed'] = $is_reviewed;
	    $doc['is_seen'] = $is_seen;
	    $doc['review_doc_ntcode'] = $review_doc_ntcode;

            array_push($docs, $doc);
        }

        return $docs;
    }


function getDocByExamType($ntcode,$exam_type){
        $stmt = $this->con->prepare("SELECT id, user_id, name, last_name, date, doctor_name, reason, lab_name
, exam_type, is_reviewed, is_seen, review_doc_ntcode FROM documents WHERE user_id = $ntcode AND exam_type = '$exam_type'");
        $id = null;
        $user_id = null;
        $name = null;
        $last_name = null;
        $date = null;
        $doctor_name = null;
        $reason = null;
        $lab_name = null;
	$exam_type = null;
	$is_reviewed = null;
	$is_seen = null;
	$review_doc_ntcode = null;
	

        $stmt->execute();
        $stmt->bind_result($id, $user_id, $name, $last_name, $date, $doctor_name,$reason,$lab_name,$exam_type,$is_reviewed, $is_seen,$review_doc_ntcode);
        $docs = array();
        $stmt->store_result();
        while($stmt->fetch()){
            $doc  = array();

            $doc['id'] = $id;
            $doc['user_id'] = $user_id;
            $doc['name'] = $name;
            $doc['last_name'] = $last_name;
            $doc['date'] = $date;
            $doc['doctor_name'] = $doctor_name;
            $doc['reason'] = $reason;
            $doc['lab_name'] = $lab_name;
	    $doc['exam_type'] = $exam_type;
	    $doc['is_reviewed'] = $is_reviewed;
	    $doc['is_seen'] = $is_seen;
	    $doc['review_doc_ntcode'] = $review_doc_ntcode;

            array_push($docs, $doc);
        }

        return $docs;
    }


    function getDocMedia($docId){
        $stmt = $this->con->prepare("SELECT media_id, doc_id, doc_url, doc_name, doc_type FROM documents_media WHERE doc_id = $docId");
        $id = null;
        $doc_id = null;
        $doc_url = null;
        $doc_name = null;
        $doc_type = null;

        $stmt->execute();
        $stmt->bind_result($id, $doc_id, $doc_url, $doc_name, $doc_type);

        $docs = array();

        while($stmt->fetch()){
            $doc  = array();
            $doc['media_id'] = $id;
            $doc['doc_id'] = $doc_id;
            $doc['doc_url'] = $doc_url;
            $doc['doc_name'] = $doc_name;
            $doc['doc_type'] = $doc_type;

            array_push($docs, $doc);
        }

        return $docs;
    }

    function SetReview($docId,$doctor_nt_code){

	    $stmt = $this->con->prepare("UPDATE documents SET is_reviewed = 1, review_doc_ntcode = '$doctor_nt_code' WHERE id = '$docId'");

	if($stmt->execute()){
		return true;
	}else{
	
		return false;
	}
    
    
    }

    function SetIsSeen($docId,$doctor_ntcode,$time){

	    $stmt = $this->con->prepare("UPDATE doc_seen_status SET is_seen = 1, time = '$time' WHERE document_id = '$docId' AND doctor_id = '$doctor_ntcode' ");

        if($stmt->execute()){
                return true;
        }else{

                return false;
        }
    }

    function SetIsSeenForUser($docId){

            $stmt = $this->con->prepare("UPDATE documents SET is_seen = 1 WHERE id = '$docId'");

        if($stmt->execute()){
                return true;
        }else{

                return false;
        }
    }

    function GetIsSeen($docId,$doctor_ntcode){
		
	    $seen = 1;

            $stmt = $this->con->prepare("SELECT * FROM doc_seen_status WHERE is_seen = '$seen' AND document_id = '$docId' AND doctor_id = '$doctor_ntcode'");
        $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows == 0){
                return false;
        }else{
                return true;
        
	}
    }

    function GetIsNotSeen($doctor_ntcode){

            $seen = 0;

            $stmt = $this->con->prepare("SELECT * FROM doc_seen_status WHERE is_seen = '$seen' AND doctor_id = '$doctor_ntcode'");
        $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows > 0){
                return $stmt->num_rows;
        }else{
                return 0;

        }
    }


    function DeleteDoc($docId){
	    $filepath="../v1/uploads/";
	    $file_name = null;
	   $getFileNameStmt = $this->con->prepare("SELECT doc_name FROM documents_media WHERE doc_id ='$docId'");
	 
	    $getFileNameStmt->execute();
	    $getFileNameStmt->bind_result($file_name);
	    $getFileNameStmt->store_result();

        while ($getFileNameStmt->fetch()){

            // activate the user
            // $this->activateUserStatus($id);

            $user = array();
            $user["doc_name"] = $file_name;
        }
	    $getFileNameStmt->close();
    	$stmt = $this->con->prepare("DELETE FROM documents WHERE id ='$docId'");
	if($stmt->execute()){
	  unlink($filepath . $file_name);
	  return true;
        }else{

                return false;
        }
    
    }

    function updateDocument($id, $name, $lastName, $date, $doctor_name, $reason,
                            $lab_name, $exam_type){
        $stmt = $this->con->prepare("UPDATE documents SET name = ?, last_name = ?, date = ?, doctor_name = ?
, reason = ?, lab_name = ?, exam_type = ? WHERE id = ?");
        $stmt->bind_param("ssssssss", $name, $lastName, $date, $doctor_name, $reason,$lab_name
            , $exam_type,$id);


        if($stmt->execute())
            return true;
        return false;
    }

    function updateDocumentMedia($media_id, $doc_name, $doc_type){


	    $filepath="../v1/uploads/";
            $file_name = null;
           $getFileNameStmt = $this->con->prepare("SELECT doc_name FROM documents_media WHERE media_id ='$media_id'");

            $getFileNameStmt->execute();
            $getFileNameStmt->bind_result($file_name);
            $getFileNameStmt->store_result();

        while ($getFileNameStmt->fetch()){

            // activate the user
            // $this->activateUserStatus($id);

            $user = array();
            $user["doc_name"] = $file_name;
        }
	    $getFileNameStmt->close();

	if(unlink($filepath . $file_name)){


	    $stmt = $this->con->prepare("UPDATE documents_media SET doc_url = ? ,doc_name = ?, doc_type = ? WHERE media_id = ?");

	$imgPath = "https://mpmed.ir/mp_app/v1/uploads/$doc_name";

        $stmt->bind_param("ssss", $imgPath, $doc_name, $doc_type, $media_id);


        if($stmt->execute()){
		return true;
	}else{
		return false;
		}
	}else{
		return false;
	}
	    return false;

    }


//    /*
//    * The update operation
//    * When this method is called the record with the given id is updated with the new given values
//    */
//    function updateDevice($id, $name, $manufactor, $priceuser, $pricevip, $color, $info
//        , $available, $category, $storage, $ram, $url, $is_available, $size, $use_case,$battery, $watt
//        , $display, $connection_type){
//        $stmt = $this->con->prepare("UPDATE devices SET name = ?, manufactor = ?, priceuser = ?, pricevip = ?
//, color = ?, info = ?, available = ?, category = ?, storage = ?, ram = ?, url = ?, is_available = ?, size = ?
//, display = ?, watt = ?, battery = ?, use_case = ?, connection_type = ? WHERE id = ?");
//        $stmt->bind_param("sssssssssssssssssss", $name, $manufactor, $priceuser, $pricevip, $color,$info
//            , $available, $category, $storage, $ram, $url, $is_available, $size, $display, $watt, $battery
//            , $use_case, $connection_type,$id);
//
//	$childstmt = $this->con->prepare("UPDATE device_child SET name = ?, manufactor = ?, info = ?, category = ?, url = ? WHERE device_id = ?");
//        $childstmt->bind_param("ssssss", $name, $manufactor, $info
//            , $category, $url, $id);
//        $childstmt->execute();
//
//        if($stmt->execute())
//            return true;
//        return false;
//    }
//
//
//    function updatePrice($id, $priceuser, $pricevip){
//        $stmt = $this->con->prepare("UPDATE devices SET  priceuser = ?, pricevip = ? WHERE id = ?");
//        $stmt->bind_param("sss", $priceuser, $pricevip, $id);
//        if($stmt->execute())
//            return true;
//        return false;
//    }
//
//
//    function updatePriceAndAvailibility($id, $priceuser, $pricevip, $isAvailable){
//        $stmt = $this->con->prepare("UPDATE devices SET  priceuser = ?, pricevip = ?, is_available = ? WHERE id = ?");
//        $stmt->bind_param("ssss", $priceuser, $pricevip, $isAvailable, $id);
//        if($stmt->execute())
//            return true;
//        return false;
//    }
//
//
//    function updatePriceSeller($id, $priceuser){
//        $stmt = $this->con->prepare("UPDATE devices SET  priceuser = ? WHERE id = ?");
//        $stmt->bind_param("ss", $priceuser, $id);
//        if($stmt->execute())
//            return true;
//        return false;
//    }
//
//
//    function updateDeviceimage($id, $name, $manufactor, $priceuser, $pricevip, $color, $info
//        , $available, $category, $storage, $image, $ram, $url, $is_available, $size, $use_case,$battery, $watt
//        , $display, $connection_type){
//
//
//
//        $imgPath = "http://izeetech.com/appdbapi/v1/uploads/$image";
//
//        $image_name = $this->getImageName($id);
//
//        unlink("/home/izeetech/public_html/appdbapi/v1/uploads/$image_name");
//
//	$stmt = $this->con->prepare("UPDATE devices SET name = ?, manufactor = ?, priceuser = ?, pricevip = ?
//, color = ?, info = ?, available = ?, category = ?, storage = ?, image = ?, ram = ?, url = ?, is_available = ?, size = ?
//, display = ?, watt = ?, battery = ?, use_case = ?, connection_type = ? WHERE id = ?");
//        $stmt->bind_param("ssssssssssssssssssss", $name, $manufactor, $priceuser, $pricevip, $color,$info
//            , $available, $category, $storage, $imgPath, $ram, $url, $is_available, $size, $display, $watt, $battery
//            , $use_case, $connection_type,$id);
//
//	$childstmt = $this->con->prepare("UPDATE device_child SET name = ?, manufactor = ?, info = ?, category = ?, image = ?, url = ? WHERE device_id = ?");
//        $childstmt->bind_param("sssssss", $name, $manufactor, $info
//            , $category, $imgPath, $url, $id);
//	$childstmt->execute();
//
//        if($stmt->execute())
//            return true;
//        return false;
//    }
//
//
//    function getImageName($id){
//        $stmt = $this->con->prepare("SELECT image_name FROM devices WHERE id = '$id' ");
//        $image_name = null;
//
//        $stmt->execute();
//        $stmt->bind_result($image_name);
//
//
//        while ($stmt->fetch()) {
//
//            $device['image_name'] = $image_name;
//
//        }
//
//        return $image_name;
//    }
//
//
//    /*
//    * The delete operation
//    * When this method is called record is deleted for the given id
//    */
//    function deleteDevice($id,$dlImage){
//
//        $image_name = $this->getImageName($id);
//
//
//        $stmt = $this->con->prepare("DELETE FROM devices WHERE id = ? ");
//        $stmt->bind_param("i", $id);
//        if($stmt->execute()){
//            if($dlImage = 1){
//
//                if (!unlink("/home/izeetech/public_html/appdbapi/v1/uploads/$image_name")) {
//                    return false;
//                }
//                else {
//                    return true;
//                }
//
//            }
//
//        }
//
//        return false;
//    }

}
