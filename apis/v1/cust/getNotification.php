<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, x-Requested-With");

require_once "../../config/Database.php";
require_once "../../model/userModel.php";
require_once "../../model/HttpResponse.php";
require_once "../../config/auth.php";

//CHECK INCOMING GATE REQUESTS
if($_SERVER['REQUEST_METHOD'] === "GET") {

    $userModel = new userModel($db);

    //FETCH TRANSACTION BY ID IF ID EXIST OR ALL IF ID DOESN'T EXIST
    $resultsData = (isset($_GET['acctId'])) ? $userModel->fetchIndivNotification($_GET['acctId']) : "Invalid Request";
    
    if($resultsData === "Invalid Request") {

        $newhttp->customResp(404, "04", "No data found");

    }else {

        $newhttp->customResp(200, "00", "Success", $resultsData);

    }

}
?>