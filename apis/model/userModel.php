<?php

class userModel {

    protected $db;
    
    public function __construct(Database $db) {

        $this->db = $db;

    }

    /** ENDPOINT URL FOR LOGIN:
     * 
     * {
     *  "username": "test",
     *  "password": "test",
     * }
     * 
    * */
    public function loginUser($parameter) {
        
        if(isset($parameter->username) && isset($parameter->password)) {
                
            $username = $parameter->username;
            
            $password = $parameter->password;

            if($username === "" || $password === ""){

                return -1;

            }else{
                
                $checkAuth = $this->db->select("user_tbl",["username"=>$username, "user_passwd"=>$password]);
                $countUser = $checkAuth->rowCount();

                if($countUser === 0){

                    return -2;

                }else{
                    
                    $getUser = $checkAuth->fetch();
                    
                    $resultsData = [
                        "weizleId" => $getUser->weizle_id,
                        "fullName" => $getUser->full_name,
                        "phoneNo" => $getUser->phone_no,
                        "userName" => $getUser->username,
                        "subId" => $getUser->subscription_id,
                        "accountType" => $getUser->account_type,
                        "myRefCode" => $getUser->myref_code,
                        "userStatus" => $getUser->user_status
                    ];

                    return $resultsData;

                }

            }

        }else{

            return -3;

        }

    }


    /** ENDPOINT URL FOR REGISTRATION:
     * 
     * {
     *  "fullName": "Akin Tester",
     *  "phoneNo": "081028928939",
     *  "userName": "test"
     *  "userPass": "test",
     *  "regCode": "xye828"
     * }
     * 
    * */
    public function regUser($parameter) {
        
        if(isset($parameter->fullName) && isset($parameter->phoneNo) && isset($parameter->userName) && isset($parameter->userPass) && isset($parameter->regCode)) {
            
            $weizleId = mt_rand(1000000000,9999999999);
            $fullName = $parameter->fullName;
            $phoneNo = $parameter->phoneNo;
            $userName = $parameter->userName;
            $userPass = $parameter->userPass;
            $regCode = strtoupper($parameter->regCode);
            $myRefCode = strtoupper(uniqid());
            $status = "Active";
            $accountType = "STANDARD";

            $checkAuth = $this->db->select("user_tbl",["username"=>$userName]);
            $countUser = $checkAuth->rowCount();

            if($fullName === "" || $phoneNo === "" || $userName === "" || $userPass === ""){

                return -1;

            }elseif($countUser != 0){

                return -2;

            }else{
                
                $searchUser = $this->db->select("user_tbl",["myref_code"=>$regCode]);
                $getUser = $searchUser->fetch();

                $insertUser = $this->db->insert("user_tbl", array("weizle_id"=>$weizleId, "full_name"=>$fullName, "phone_no"=>$phoneNo, "username"=>$userName, "user_passwd"=>$userPass, "myref_code"=>$myRefCode, "referral_code"=>$regCode, "account_type"=>$accountType, "user_status"=>$status));

                if($insertUser){

                    $resultsData = [
                        "weizleId" => $weizleId,
                        "fullName" => $fullName,
                        "phoneNo" => $phoneNo,
                        "userName" => $userName,
                        "accountType" => $accountType,
                        "myRefCode" => $myRefCode,
                        "userStatus" => $status,
                        "referral"=> $getUser->full_name . '(' . $regCode . ')'
                    ];

                    return $resultsData;

                }else{

                    return -4;

                }

            }

        }else{

            return -3;

        }

    }


    /** ENDPOINT URL FOR ACCOUNT LOOKUP:
     * 
     * {
     *  "accountId": "test" //username OR weizleId
     * }
     * 
    * */
    public function acctLookup($parameter) {
        
        if(isset($parameter->accountId)) {
                
            $accountId = $parameter->accountId;

            if($accountId === ""){

                return -1;

            }else{
                
                $searchUser = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$accountId,":wid"=>$accountId));
                $countUser = $searchUser->rowCount();

                if($countUser === 0){

                    return -2;

                }else{
                    
                    $getUser = $searchUser->fetch();

                    $resultsData = [
                        "weizleId" => $getUser->weizle_id,
                        "fullName" => $getUser->full_name,
                        "phoneNo" => $getUser->phone_no,
                        "userName" => $getUser->username,
                        "secQuestion" => $getUser->security_question,
                        "accountType" => $getUser->account_type,
                        "myRefCode" => $getUser->myref_code,
                        "userStatus" => $getUser->user_status
                    ];

                    return $resultsData;

                }

            }

        }else{

            return -3;

        }

    }
    

    /** ENDPOINT URL TO RESET PASSWORD:
     * 
     * {
     *  "userName": "test",
     *  "secAnswer": "xxx",
     *  "userPass": "test"
     * }
     * 
    * */
    public function resetPassWd($parameter) {
        
        if(isset($parameter->userName) && isset($parameter->secAnswer) && isset($parameter->userPass)) {
                
            $userName = $parameter->userName;
            $secAnswer = $parameter->secAnswer;
            $userPass = $parameter->userPass;

            $checkAuth = $this->db->select("user_tbl",["username"=>$userName, "security_answer"=>$secAnswer]);
            $countUser = $checkAuth->rowCount();

            if($userName === "" || $secAnswer === "" || $userPass === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }else{
                
                $updateUserPW = $this->db->update("user_tbl",array("user_passwd"=>$userPass),array("username"=>$userName,"security_answer"=>$secAnswer));

                if($updateUserPW){

                    $resultsData = [
                        "responseMessage" => "Password reset successfully"
                    ];

                    return $resultsData;

                }else{

                    return -4;

                }

            }

        }else{

            return -3;

        }

    }


    /** ENDPOINT URL TO SET SECURITY QUESTION:
     * 
     * {
     *  "userName": "test",
     *  "secQuestion": "xxxxxxx",
     *  "secAnswer": "xxx"
     * }
     * 
    * */
    public function setSQA($parameter) {
        
        if(isset($parameter->userName) && isset($parameter->secQuestion) && isset($parameter->secAnswer)) {
                
            $userName = $parameter->userName;
            $secQuestion = $parameter->secQuestion;
            $secAnswer = $parameter->secAnswer;

            $checkAuth = $this->db->select("user_tbl",["username"=>$userName]);
            $countUser = $checkAuth->rowCount();

            if($userName === "" || $secQuestion === "" || $secAnswer === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }else{
                
                $updateUserSQA = $this->db->update("user_tbl",array("security_question"=>$secQuestion,"security_answer"=>$secAnswer),array("username"=>$userName));

                if($updateUserSQA){

                    $resultsData = [
                        "responseMessage" => "Security question set successfully"
                    ];

                    return $resultsData;

                }else{

                    return -4;

                }

            }

        }else{

            return -3;

        }

    }


    /** ENDPOINT URL TO UPDATE PROFILE:
     * 
     * {
     *  "userName": "test",
     *  "fullName": "xxxxxxx",
     *  "phoneNo": "xxx"
     * }
     * 
    * */
    public function updateProfile($parameter) {
        
        if(isset($parameter->userName) && isset($parameter->fullName) && isset($parameter->phoneNo)) {
                
            $userName = $parameter->userName;
            $fullName = $parameter->fullName;
            $phoneNo = $parameter->phoneNo;

            $checkAuth = $this->db->select("user_tbl",["username"=>$userName]);
            $countUser = $checkAuth->rowCount();

            if($userName === "" || $fullName === "" || $phoneNo === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }else{
                
                $updateUserPro = $this->db->update("user_tbl",array("full_name"=>$fullName,"phone_no"=>$phoneNo),array("username"=>$userName));

                if($updateUserPro){
        
                    $resultsData = [
                        "responseMessage" => "Profile updated successfully"
                    ];
        
                    return $resultsData;
        
                }else{
        
                    return -4;
        
                }

            }

        }else{

            return -3;

        }

    }


    /** ENDPOINT URL FOR NOTIFICATION:
     * 
     * {
     *  "accountId": "test",
     *  "emergencySetupId": "22",
     *  "emergencyState": "Oyo"
     *  "emergencyCity": "Ibadan",
     *  "longitude": "-17727",
     *  "latitude": "92899",
     *  "fullAddress": "Mobolaji Junction...",
     *  "emergencyCountry": "Nigeria"
     * }
     * 
    * */
    public function weizleNotifier($parameter) {
        
        if(isset($parameter->accountId) && isset($parameter->emergencySetupId) && isset($parameter->emergencyState) && isset($parameter->emergencyCity) && isset($parameter->longitude) && isset($parameter->latitude) && isset($parameter->fullAddress) && isset($parameter->emergencyCountry)) {
            
            $accountId = $parameter->accountId;
            $emergencySetupId = $parameter->emergencySetupId;
            $emergencyState = $parameter->emergencyState;
            $emergencyCity = $parameter->emergencyCity;
            $longitude = $parameter->longitude;
            $latitude = $parameter->latitude;
            $fullAddress = $parameter->fullAddress;
            $emergencyCountry = $parameter->emergencyCountry;

            $checkAuth = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$accountId,":wid"=>$accountId));
            $countUser = $checkAuth->rowCount();

            $checkEmergSetup = $this->db->select("emergency_setup_tbl",["emergency_id"=>$emergencySetupId,"setupStatus"=>"Active"]);
            $countEmergSetup = $checkEmergSetup->rowCount();

            if($accountId === "" || $emergencySetupId === "" || $emergencyState === "" || $emergencyCity === "" || $longitude === "" || $latitude === "" || $fullAddress === "" || $emergencyCountry === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }elseif($countEmergSetup === 0){

                return -5;

            }else{
                
                $getEmergSetup = $checkEmergSetup->fetch();
                $userList = $getEmergSetup->username_list;
                $phoneList = $getEmergSetup->phone_list;
                $emergencyCat = $getEmergSetup->emergency_category;
                $emergencyMsg = $getEmergSetup->emergency_msg;

                $sendNotification = $this->db->insert("notification_tbl", array("account_id"=>$accountId, "emergency_setup_id"=>$emergencySetupId, "emergency_msg"=>$emergencyMsg, "username_list"=>$userList, "phone_list"=>$phoneList, "emergency_state"=>$emergencyState, "emergency_city"=>$emergencyCity, "longitude"=>$longitude, "latitude"=>$latitude, "full_address"=>$fullAddress, "emergency_country"=>$emergencyCountry, "notification_status"=>"Sent", "createdDate"=>date("Y-m-d H:i:s")));

                if($sendNotification){

                    $resultsData = [
                        "emergencyCat" => $emergencyCat,
                        "emergencyMsg" => $emergencyMsg,
                        "notificationStatus" => "Sent",
                        "createdDate"=> date("Y-m-d H:i:s")
                    ];

                    return $resultsData;

                }else{

                    return -4;

                }

            }

        }else{

            return -3;

        }

    }


    //FETCH INDIVIDUAL NOTIFICATION
    public function fetchIndivNotification($parameter) {
        
        $searchNotification = $this->db->query("select * from notification_tbl where username_list LIKE '%:userList%'", array(":userList"=>$parameter));
        $countNotification = $searchNotification->rowCount();

        for($i = 0; $i <= $countNotification; $i++){

            while($getNotification = $searchNotification->fetch()){

                $emergencySetupId = $getNotification->emergency_setup_id;
                $searchNType = $this->db->select("emergency_setup_tbl", ["emergency_id"=>$emergencySetupId]);
                $emergencyCat = $searchNType->emergency_category;

                $output[$i] = [
                    "id" => $getNotification->id,
                    "emergencyCategory" => $emergencyCat,
                    "emergencyMsg" => $getNotification->emergency_msg,
                    "fullAddress" => $getNotification->full_address,
                    "eventState" => $getNotification->emergency_state,
                    "eventCity" => $getNotification->emergency_city,
                    "country" => $getNotification->emergency_country,
                    "longitude" => $getNotification->longitude,
                    "latitude" => $getNotification->latitude,
                    "notificationStatus" => $getNotification->notification_status,
                    "createdOn" => $getNotification->createdDate
                ];
                $i++;
    
            }
            return ($output == null) ? "Invalid Request" : $output;

        }

    }


}

?>