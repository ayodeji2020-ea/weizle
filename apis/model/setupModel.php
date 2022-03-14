<?php

class setupModel {

    protected $db;
    
    public function __construct(Database $db) {

        $this->db = $db;

    }

    //FETCH ALL EMMERGENCY CATEGORY
    public function fetchAllECat() {
        
        $searchECat = $this->db->query("select * from emergency_cat_tbl");
        $countECat = $searchECat->rowCount();

        for($i = 0; $i <= $countECat; $i++){

            while($getECat = $searchECat->fetch()){

                $output[$i] = [
                    "id" => $getECat->id,
                    "catName" => $getECat->cat_name,
                    "defaultMsg" => $getECat->default_msg
                ];
                $i++;
    
            }
            return $output;

        }

    }

    /** ENDPOINT URL FOR EMERGENCY SETUP:
     * 
     * {
     *  "accountId": "test", //username or weizleId
     *  "emergencyCat": "Medical",
     *  "emergencyMsg": "I need midical help",
     *  "userNames": "mytest,wizkid,demo",
     *  "userContacts": "+23481028928939,+441028911111"
     * }
     * 
    * */
    public function setupEmergency($parameter) {
        
        if(isset($parameter->accountId) && isset($parameter->emergencyCat) && isset($parameter->emergencyMsg) && isset($parameter->userNames) && isset($parameter->userContacts)) {
                
            $accountId = $parameter->accountId;
            $emergencyCat = $parameter->emergencyCat;
            $emergencyMsg = $parameter->emergencyMsg;
            $userNames = $parameter->userNames;
            $userContacts = $parameter->userContacts;
            $dateTime = date("Y-m-d H:i:s");

            $checkAuth = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$accountId,":wid"=>$accountId));
            $countUser = $checkAuth->rowCount();

            if($accountId === "" || $emergencyCat === "" || $emergencyMsg === "" || $userNames === "" || $userContacts === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }else{
                
                $setup_id = strtoupper(uniqid());
                $insertSetup = $this->db->insert("emergency_setup_tbl", array("account_id"=>$accountId, "emergency_id"=>$setup_id, "emergency_category"=>$emergencyCat, "emergency_msg"=>$emergencyMsg, "phone_list"=>$userContacts, "username_list"=>$userNames, "setupStatus"=>"Pending", "createdDate"=>$dateTime));

                if($insertSetup){

                    $resultsData = [
                        "accountId" => $accountId,
                        "setupId" => $setup_id,
                        "responseMessage" => "Emergency setup successfully"
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


    //FETCH ALL PREMIUM PLAN
    public function fetchPremiumPlans($parameter) {
        
        $searchPPlan = $this->db->query("select * from premium_setup_tbl where sys_currency=:sysCur", array(":sysCur" => $parameter));
        $countPPlan = $searchPPlan->rowCount();

        for($i = 0; $i <= $countPPlan; $i++){

            while($getPPlan = $searchPPlan->fetch()){

                $output[$i] = [
                    "id" => $getPPlan->id,
                    "planFreq" => $getPPlan->plan_frequency,
                    "sysCurrency" => $getPPlan->sys_currency,
                    "planAmount" => $getPPlan->plan_amount
                ];
                $i++;
    
            }
            return ($output == null) ? "Invalid Request" : $output;

        }

    }

    /** ENDPOINT URL FOR EMERGENCY SUBSCRIPTION:
     * 
     * {
     *  "accountId": "test", //username or weizleId
     *  "emergencySetupId": "49",
     *  "countryName": "Nigeria",
     *  "phoneNumber": "+23490127387388",
     *  "premiumPlanId": "122",
     *  "planAmt": "2000",
     *  "planCurrency": "NGN"
     * }
     * 
    * */
    public function createSub($parameter) {
        
        if(isset($parameter->accountId) && isset($parameter->emergencySetupId) && isset($parameter->countryName) && isset($parameter->phoneNumber) && isset($parameter->premiumPlanId) && isset($parameter->planAmt) && isset($parameter->planCurrency)) {
                
            $accountId = $parameter->accountId;
            $emergencySetupId = $parameter->emergencySetupId;
            $countryName = $parameter->countryName;
            $phoneNumber = $parameter->phoneNumber;
            $premiumPlanId = $parameter->premiumPlanId;
            $planAmt = $parameter->planAmt;
            $planCurrency = $parameter->planCurrency;
            $dateTime = date("Y-m-d H:i:s");

            $checkAuth = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$accountId,":wid"=>$accountId));
            $countUser = $checkAuth->rowCount();

            if($accountId === "" || $emergencySetupId === "" || $countryName === "" || $phoneNumber === "" || $premiumPlanId === "" || $planAmt === "" || $planCurrency === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }else{
                
                $sub_id = strtoupper(uniqid("Sub"));
                $insertSub = $this->db->insert("subscription_tbl", array("account_id"=>$accountId, "sub_id"=>$sub_id, "premium_id"=>$premiumPlanId, "emergency_setup_id"=>$emergencySetupId, "plan_amount"=>$planAmt, "sys_currency"=>$planCurrency, "emergency_country"=>$countryName, "contactNumber"=>$phoneNumber, "sub_status"=>"Pending", "createdDate"=>$dateTime));

                if($insertSub){

                    $resultsData = [
                        "subId" => $sub_id,
                        "emergencySetupId" => $emergencySetupId,
                        "subStatus" => "Pending",
                        "responseMessage" => "Subscription in processing..."
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


    /** ENDPOINT URL TO CONFIRM EMERGENCY SUBSCRIPTION PAYMENT:
     * 
     * {
     *  "accountId": "test", //username or weizleId
     *  "SubId": "49",
     *  "txtRef": "dsgg-3hbbdb",
     *  "paymentStatus": "Approved"
     * }
     * 
    * */
    public function confirmSub($parameter) {
        
        if(isset($parameter->accountId) && isset($parameter->SubId) && isset($parameter->txtRef) && isset($parameter->paymentStatus)) {
                
            $accountId = $parameter->accountId;
            $SubId = $parameter->SubId;
            $txtRef = $parameter->txtRef;
            $paymentStatus = $parameter->paymentStatus;

            $checkAuth = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$accountId,":wid"=>$accountId));
            $countUser = $checkAuth->rowCount();

            if($accountId === "" || $SubId === "" || $txtRef === "" || $paymentStatus === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;

            }elseif($paymentStatus === "Approved"){

                $checkSub = $this->db->select("subscription_tbl",["sub_id"=>$SubId, "sub_status"=>"Pending"]);
                $getSub = $checkSub->fetch();
                $premiumPlanId = $getSub->premium_id;
                $emergencySetupId = $getSub->emergency_setup_id;

                $checkPrem = $this->db->select("premium_setup_tbl",["id"=>$premiumPlanId]);
                $getPrem = $checkPrem->fetch();
                $planFreq = $getPrem->plan_frequency;

                //Calculate Next Payment Date
                $startDate = date("Y-m-d H:i:s");
                $premiumInterval = ($planFreq == "Monthly" ? 30 : ($planFreq == "Quaterly" ? 90 : ($planFreq == "Annual" ? 360 : 30)));
                $expiredDate = date('Y-m-d H:i:s', strtotime('+'.$premiumInterval.' day', strtotime($startDate)));
                
                $updateEmergency = $this->db->update("emergency_setup_tbl",array("setupStatus"=>"Active","startDate"=>$startDate,"expiredDate"=>$expiredDate),array("emergency_id"=>$emergencySetupId));
                $updateSub = $this->db->update("subscription_tbl",array("txtRef"=>$txtRef,"sub_status"=>$paymentStatus),array("sub_id"=>$SubId));

                if($updateEmergency && $updateSub){

                    $resultsData = [
                        "subRef" => $txtRef,
                        "responseMessage" => "Subscription Approved Successfully"
                    ];

                    return $resultsData;

                }else{

                    return -4;

                }

            }else{

                return -5;

            }

        }else{

            return -3;

        }

    }


}

?>