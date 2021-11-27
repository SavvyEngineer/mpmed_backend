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
/**
 * @OA\Info(title="My First API", version="0.1")
 */


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
//if it is an api call
//that means a get parameter named api call is set in the URL
//and with this parameter we are concluding that it is an api call
if(isset($_GET['apicall'])){

    switch($_GET['apicall']){

        //the CREATE operation
        //if the api call value is 'createhero'
        //we will create a record in the database
 
/**
 * @OA\Post(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="An example resource")
 * )
 */

       case 'createaccess':

            //first check the parameters required for this request are available or not

                //creating a new dboperation object

                $db = new DbOperation();

                //creating a new record in the database
                $result = $db->createAccess(
			$_POST['doc_nt_code'],
			$_POST['user_ntcode'],
                    $_POST['accessed_doc'],
                    $_POST['time']
                );
	
                //if the record is created adding success to response
                if($result){

                    //record is created means there is no error
                    $response['error'] = false;

                    //in message we have a success message
                    $response['message'] = 'Access addedd successfully';

                    //and we are getting all the heroes from the database in the response
                  //  $response['devices'] = $db->getDevices();
                }else{

                    //if record is not added that means there is an error
                    $response['error'] = true;

                    //and we have the error message
                    $response['message'] = 'Some error occurred please try again';
                }


            break;


        //the READ operation
        //if the call is getheroes
        case 'getaccesstable':
            $db = new DbOperation();
            // $response['error'] = false;
            //  $response['message'] = 'Request successfully completed';
            //  $response['devices'] = $db->getDevices();
	   // $response = $db->getAccessTable($_GET['doc_nt_code']);
	    
	    	$response = $db->getAccessTable($_GET['doc_nt_code']);
	    

	    break;

	case 'check_access':
	   $db = new DbOperation();
	   $result = $db->CheckIfRowExists($_POST['doc_nt_code'],$_POST['accessed_doc']);

	//   $response['message'] = $result;
	   if($result){
	   	$response['error'] = false;

                    //and we have the error message
                    $response['message'] = 'Doctor has access';
	   }else{
	   $response['error'] = true;

                    //and we have the error message
                    $response['message'] = 'No Access Yet';
	    }
	   break;
    }

}else{
    //if it is not api call
    //pushing appropriate values to response array
    $response['error'] = true;
    $response['message'] = 'Invalid API Call';
}

//displaying the response in json structure
echo json_encode($response, JSON_PRETTY_PRINT);
