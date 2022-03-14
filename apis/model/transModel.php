<?php

class transModel {

    protected $db;
    
    public function __construct(Database $db) {

        $this->db = $db;

    }

    //FETCH SUBSCRIPTION HISTORY
    public function fetchSubHistory($parameter) {

        $checkAuth = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$parameter,":wid"=>$parameter));
        $countUser = $checkAuth->rowCount();

        $searchSub = $this->db->query("select * from subscription_tbl where account_id=:userId", array(":userId" => $parameter));
        $countSub = $searchSub->rowCount();

        if($countUser === 0){

            return -1;
                    
        }elseif($countSub >= 1){

            for($i = 0; $i <= $countSub; $i++){

                while($getSub = $searchSub->fetch()){
    
                    $output[$i] = [
                        "id" => $getSub->id,
                        "txtRef" => $getSub->sub_id,
                        "planAmt" => $getSub->plan_amount,
                        "planCurrency" => $getSub->sys_currency,
                        "emergencyCountry" => $getSub->emergency_country,
                        "emergencyCountry" => $getSub->contactNumber,
                        "subStatus" => $getSub->sub_status,
                        "createdDate" => $getSub->createdDate
                    ];
                    $i++;
        
                }
                return $output;
    
            }

        }else{

            return -2;

        }

    }


    /** ENDPOINT URL TO FETCH SUB HISTORY WITH DATE:
     * 
     * {
     *  "startDate" : "2020-01-01",
     *  "endDate" : "2020-20-01",
     *  "accountId" : "test"
    *  }
     * 
     * */
    public function fetchSubHistoryByDate($parameter) {

        if(isset($parameter->startDate) && isset($parameter->endDate) && isset($parameter->accountId)) {

            $myStartDate = $parameter->startDate;
            $myEndDate = $parameter->endDate;
            $accountId = $parameter->accountId;

            $startDate = $myStartDate.' 00:00:00'; // get start date from here
            $endDate = $myEndDate.' 24:00:00';

            $checkAuth = $this->db->query("select * from user_tbl where username=:uname OR weizle_id=:wid",array(":uname"=>$accountId,":wid"=>$accountId));
            $countUser = $checkAuth->rowCount();

            $searchSub = $this->db->query("select * from subscription_tbl where createdDate BETWEEN :startDate AND :endDate AND account_id=:uname", array(":startDate"=>$startDate, ":endDate"=>$endDate, ":uname"=>$accountId));
            $countSub = $searchSub->rowCount();

            if($myStartDate === "" || $myEndDate === "" || $accountId === ""){

                return -1;

            }elseif($countUser === 0){

                return -2;
                        
            }elseif($countSub >= 1){

                for($i = 0; $i <= $countSub; $i++){
    
                    while($getSub = $searchSub->fetch()){
        
                        $output[$i] = [
                            "id" => $getSub->id,
                            "txtRef" => $getSub->sub_id,
                            "planAmt" => $getSub->plan_amount,
                            "planCurrency" => $getSub->sys_currency,
                            "emergencyCountry" => $getSub->emergency_country,
                            "emergencyCountry" => $getSub->contactNumber,
                            "subStatus" => $getSub->sub_status,
                            "createdDate" => $getSub->createdDate
                        ];
                        $i++;
            
                    }
                    return $output;
        
                }
    
            }else{
    
                return -3;
    
            }
    
        }else{

            return -4;

        }

    }



}

?>