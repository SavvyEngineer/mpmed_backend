<?php

//getting the dboperation class
require_once '../includes/DbOperation.php';

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
        case 'create_user':

            //first check the parameters required for this request are available or not
       //     isTheseParametersAvailable(array('user_id'));
                //creating a new dboperation object

                $db = new DbOperation();

                //creating a new record in the database
                $result = $db->createUser(
                    $_POST['doctor_ntcode'],
                    $_POST['user_name'],
                    $_POST['user_last_name'],
                    $_POST['time'],
		    $_POST['user_ntcode'],
		    $_POST['content']
                );
	
                //if the record is created adding success to response
                if($result){

                    //record is created means there is no error
                    $response['error'] = false;

                    //in message we have a success message
                    $response['message'] = 'Patient addedd successfully';

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
        case 'get_users':
            $db = new DbOperation();
            // $response['error'] = false;
            //  $response['message'] = 'Request successfully completed';
            //  $response['devices'] = $db->getDevices();
            $response = $db->getUsers($_POST['doctor_ntcode']);

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
