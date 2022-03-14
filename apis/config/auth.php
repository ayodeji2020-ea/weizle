<?php

$db = new Database();
$newhttp = new HttpResponse();

/**
 * get authorization header
 * */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * get access token from header
 * */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

if(getAuthorizationHeader() == "" || getBearerToken() == ""){

    $newhttp->customResp(401, "01", "Authorization should not be empty", $resultsData = "");
    exit();

}else{

    $bearerToken = getBearerToken();

    $systemDetails = $db->select("system_settings_tbl");
    $getSysDetails = $systemDetails->fetch();
    $correctToken = $getSysDetails->app_key;

    if($bearerToken != $correctToken){

        $newhttp->customResp(401, "01", "Unauthorized access", $resultsData = "");
        exit();

    }else{

        $bearerToken = $correctToken;
        $sysCurrency = $systemDetails->sys_currency;

    }

}

?>