<?php

//getting the dboperation class
require_once '../includes/DbOperation.php';

// Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }

//function validating all the paramters are available
//we will pass the required parameters to this function
function isTheseParametersAvailable($params){
    //assuming all parameters are available
    $available = true;
    $missingparams = "";

    foreach($params as $param){
        if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
            $available = false;
            $missingparams = $missingparams . ", " . $param;
        }
    }

    //if parameters are missing
    if(!$available){
        $response = array();
        $response['error'] = true;
        $response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';

        //displaying error
        echo json_encode($response);

        //stopping further execution
        die();
    }
}

//an array to display response
$response = array();
define('UPLOAD_PATH', 'uploads/');
//if it is an api call
//that means a get parameter named api call is set in the URL
//and with this parameter we are concluding that it is an api call
if(isset($_GET['apicall'])){

    switch($_GET['apicall']){

        //the CREATE operation
        //if the api call value is 'createhero'
        //we will create a record in the database
        case 'createdoc':

            //first check the parameters required for this request are available or not
            isTheseParametersAvailable(array('user_id'));

            try{
                //creating a new dboperation object

                $db = new DbOperation();

                //creating a new record in the database
                $result = $db->createDocument(
                    $_POST['user_id'],
                    $_POST['name'],
                    $_POST['last_name'],
                    $_POST['date'],
                    $_POST['doctor_name'],
                    $_POST['reason'],
                    $_POST['lab_name'],
		    $_POST['exam_type']
                );

		
		$response['id'] = $result;
	
                //if the record is created adding success to response
                if($result){

                    //record is created means there is no error
                    $response['error'] = false;

                    //in message we have a success message
                    $response['message'] = 'Document addedd successfully';

                    //and we are getting all the heroes from the database in the response
                  //  $response['devices'] = $db->getDevices();
                }else{

                    //if record is not added that means there is an error
                    $response['error'] = true;

                    //and we have the error message
                    $response['message'] = 'Some error occurred please try again';
                }
            }catch(Exception $e){
                $response['error'] = true;
                $response['message'] = 'Could not upload file';
            }


	    break;

	case 'get_doctors_list':
	$db = new DbOperation();
	$result = $db->getDoctorsList();
	$response = $result;

	    break;	


        case 'uploaddocmedia':

            //first check the parameters required for this request are available or not
            isTheseParametersAvailable(array('doc_id'));

            try{
                //creating a new dboperation object

                $db = new DbOperation();

                //creating a new record in the database
                $result = $db->uploadImageDoc(
                    $_POST['doc_id'],
		    $_POST['doc_type'],
                    $_FILES['media']['name']
                );

                move_uploaded_file($_FILES['media']['tmp_name'], UPLOAD_PATH . $_FILES['media']['name']);

                //if the record is created adding success to response
                if($result){

                    //record is created means there is no error
                    $response['error'] = false;

                    //in message we have a success message
                    $response['message'] = 'Media uploaded successfully';

                    //and we are getting all the heroes from the database in the response
                }else{

                    //if record is not added that means there is an error
                    $response['error'] = true;

                    //and we have the error message
                    $response['message'] = 'Some error occurred please try again';
                }
            }catch(Exception $e){
                $response['error'] = true;
                $response['message'] = 'Could not upload file';
            }


            break;

        //the READ operation
        //if the call is getheroes
        case 'getdoc':
            $db = new DbOperation();
            // $response['error'] = false;
            //  $response['message'] = 'Request successfully completed';
            //  $response['devices'] = $db->getDevices();
            $response = $db->getDoc($_GET['ntcode']);

	    break;

	case 'set_is_reviewed':
		$db = new DbOperation();

		$result = $db->SetReview($_POST['doc_id'],$_POST['doc_ntcode']);
		if($result){
			$response['error'] = false;
			$response['message'] = 'Review Started';
			
		}else{
			$response['error'] = true;
			$response['message'] = 'Something went wrong';
		}
		break;
	case 'set_is_seen':
		$db = new DbOperation();

	$result = $db->SetIsSeen($_POST['doc_id'],$_POST['doctor_ntcode'],$_POST['time']);
                if($result){
                        $response['error'] = false;
                        $response['message'] = 'Document status updated';

                }else{
                        $response['error'] = true;
                        $response['message'] = 'Something went wrong';
                }
		break;
	case 'set_is_seen_for_user':
                $db = new DbOperation();

        $result = $db->SetIsSeenForUser($_POST['doc_id']);
                if($result){
                        $response['error'] = false;
                        $response['message'] = 'Document status updated';

                }else{
                        $response['error'] = true;
                        $response['message'] = 'Something went wrong';
                }
                break;
	case 'get_is_seen':
                $db = new DbOperation();

        $result = $db->GetIsSeen($_POST['doc_id'],$_POST['doctor_ntcode']);
                if($result){
                        $response['error'] = false;
                        $response['status'] = '1';

                }else{
                        $response['error'] = true;
                        $response['status'] = '0';
                }
		break;
	case 'get_is_not_seen':
                $db = new DbOperation();

        $response['not_seen_index'] = $db->GetIsNotSeen($_POST['doctor_ntcode']);
                
         break;

	case 'getdocbydocid':
            $db = new DbOperation();
            // $response['error'] = false;
            //  $response['message'] = 'Request successfully completed';
            //  $response['devices'] = $db->getDevices();
            $response = $db->getDocByDocId($_GET['docid']);

            break;
	
	case 'getdocByExamType':
            $db = new DbOperation();
            // $response['error'] = false;
            //  $response['message'] = 'Request successfully completed';
            //  $response['devices'] = $db->getDevices();
            $response = $db->getDocByExamType($_GET['ntcode'],$_GET['exam_type']);

            break;

        case 'getdocmedia':
            $db = new DbOperation();
            // $response['error'] = false;
            //  $response['message'] = 'Request successfully completed';
            //  $response['devices'] = $db->getDevices();
            $response = $db->getDocMedia($_GET['doc_id']);

	    break;

	case 'delete_doc':
		$db = new DbOperation();

		$result = $db->DeleteDoc($_POST['doc_id']);
		if($result){
		$response['error'] = false;
		$response['message'] = "document deleted successfuly"; 
		}else{
			$response['error'] = true;
                $response['message'] = "some error occurred please try again";
			}
		break;
	case 'update_doc':
                $db = new DbOperation();

                $result = $db->updateDocument($_POST['doc_id'],$_POST['name'],$_POST['last_name'],$_POST['date'],$_POST['doctor_name'],$_POST['reason'],$_POST['lab_name'],$_POST['exam_type']);
                if($result){
                $response['error'] = false;
                $response['message'] = "document updated successfuly";
                }else{
                        $response['error'] = true;
                $response['message'] = "some error occurred please try again";
		}

		break;

	case 'update_doc_media':

		$db = new DbOperation();

		$result = $db->updateDocumentMedia($_POST['media_id'],$_FILES['media']['name'],$_POST['doc_type']);


               
		if($result){
move_uploaded_file($_FILES['media']['tmp_name'], UPLOAD_PATH . $_FILES['media']['name']);
                $response['error'] = false;
                $response['message'] = "document media updated successfuly";
                }else{
                        $response['error'] = true;
                $response['message'] = "some error occurred please try again";
                }

                break;
	break;	


//        //the UPDATE operation
//        case 'updatedevice':
//            isTheseParametersAvailable(array('id','name'));
//            $db = new DbOperation();
//
//            $result = $db->updateDevice(
//                $_POST['id'],
//                $_POST['name'],
//                $_POST['manufactor'],
//                $_POST['priceuser'],
//                $_POST['pricevip'],
//                $_POST['color'],
//                $_POST['info'],
//                $_POST['available'],
//                $_POST['category'],
//                $_POST['storage'],
//                $_POST['ram'],
//                $_POST['url'],
//                $_POST['is_available'],
//                $_POST['size'],
//                $_POST['use_case'],
//                $_POST['battery'],
//                $_POST['watt'],
//                $_POST['display'],
//                $_POST['connection_type']
//            );
//
////       move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $_FILES['image']['name']);
//
//            if($result){
//                $response['error'] = false;
//                $response['message'] = 'Device updated successfully';
//                $response['devices'] = $db->getDevices();
//            }else{
//                $response['error'] = true;
//                $response['message'] = 'Some error occurred please try again';
//            }
//            break;
//
//        case 'updateprice':
//            isTheseParametersAvailable(array('id','priceuser','pricevip'));
//            $db = new DbOperation();
//
//            $result = $db->updatePrice(
//                $_POST['id'],
//                $_POST['priceuser'],
//                $_POST['pricevip']
////              $_FILES['image']['name']
//            );
//
////       move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $_FILES['image']['name']);
//
//            if($result){
//                $response['error'] = false;
//                $response['message'] = 'Device updated successfully';
//                $response['devices'] = $db->getDevices();
//            }else{
//                $response['error'] = true;
//                $response['message'] = 'Some error occurred please try again';
//            }
//            break;
//
//
//        case 'updatePriceAndAvailibility':
//            isTheseParametersAvailable(array('id','priceuser','pricevip'));
//            $db = new DbOperation();
//
//            $result = $db->updatePriceAndAvailibility(
//                $_POST['id'],
//                $_POST['priceuser'],
//                $_POST['pricevip'],
//                $_POST['is_available']
////              $_FILES['image']['name']
//            );
//
////       move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $_FILES['image']['name']);
//
//            if($result){
//                $response['error'] = false;
//                $response['message'] = 'Device updated successfully';
//                $response['devices'] = $db->getDevices();
//            }else{
//                $response['error'] = true;
//                $response['message'] = 'Some error occurred please try again';
//            }
//            break;
//
//        case 'updatepriceseller':
//            isTheseParametersAvailable(array('id','priceuser'));
//            $db = new DbOperation();
//
//            $result = $db->updatePriceSeller(
//                $_POST['id'],
//                $_POST['priceuser']
////              $_FILES['image']['name']
//            );
//
////       move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $_FILES['image']['name']);
//
//            if($result){
//                $response['error'] = false;
//                $response['message'] = 'Device updated successfully';
//                $response['devices'] = $db->getDevices();
//            }else{
//                $response['error'] = true;
//                $response['message'] = 'Some error occurred please try again';
//            }
//            break;
//
//
//        case 'updatedeviceimage':
//            isTheseParametersAvailable(array('id','name'));
//
//		try{
//
//            $db = new DbOperation();
//
//            $result = $db->updateDeviceimage(
//                $_POST['id'],
//                $_POST['name'],
//                $_POST['manufactor'],
//                $_POST['priceuser'],
//                $_POST['pricevip'],
//                $_POST['color'],
//                $_POST['info'],
//                $_POST['available'],
//                $_POST['category'],
//                $_POST['storage'],
//                $_FILES['image']['name'],
//                $_POST['ram'],
//                $_POST['url'],
//                $_POST['is_available'],
//                $_POST['size'],
//                $_POST['use_case'],
//                $_POST['battery'],
//                $_POST['watt'],
//                $_POST['display'],
//                $_POST['connection_type']
//            );
//
//            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $_FILES['image']['name']);
//
//            if($result){
//
//                $response['error'] = false;
//                $response['message'] = 'Device updated successfully';
//                $response['devices'] = $db->getDevices();
//
//            }else{
//                $response['error'] = true;
//                $response['message'] = 'Some error occurred please try again';
//            }
//	}catch(Exception $e){
//                $response['error'] = true;
//                $response['message'] = 'Could not upload file';
//            }
//            break;
//
//
//        //the delete operation
//        case 'deletedevice':
//
//            //for the delete operation we are getting a GET parameter from the url having the id of the record to be deleted
//            if(isset($_GET['id'])){
//                $db = new DbOperation();
//                if($db->deleteDevice($_GET['id'],$_GET['delete_image'])){
//                    $response['error'] = false;
//                    $response['message'] = 'Hero deleted successfully';
//                    $response['devices'] = $db->getDevices();
//                }else{
//                    $response['error'] = true;
//                    $response['message'] = 'Some error occurred please try again';
//                }
//            }else{
//                $response['error'] = true;
//                $response['message'] = 'Nothing to delete, provide an id please';
//            }
//            break;
    }

}else{
    //if it is not api call
    //pushing appropriate values to response array
    $response['error'] = true;
    $response['message'] = 'Invalid API Call';
}

//displaying the response in json structure
echo json_encode($response, JSON_PRETTY_PRINT);
