<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,x-Requested-With");

require_once "../../config/Database.php";
require_once "../../model/transModel.php";
require_once "../../model/HttpResponse.php";
require_once "../../config/auth.php";

//CHECK INCOMING GATE REQUESTS
if($_SERVER['REQUEST_METHOD'] === "POST") {

    $paramReceived = json_decode(file_get_contents("php://input"));

    $transModel = new transModel($db);

    $results = $transModel->fetchSubHistoryByDate($paramReceived);

    if($results === -1){

        $newhttp->customResp(400, "04", "Required field must not be empty");

    }elseif($results === -2){

        $newhttp->customResp(401, "01", "Invalid account");

    }elseif($results === -3){

        $newhttp->customResp(404, "04", "No data found");

    }elseif($results === -4){
        
        $newhttp->customResp(400, "04", "Invalid JSON request");
        
    }else{

        $newhttp->customResp(200, "00", "Success", $results);
        
    }

}