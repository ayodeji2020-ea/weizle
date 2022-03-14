<?php
class HttpResponse {

    /**
     * HTTP STATUS CODE:
     * 
     * Bad Request - 400
     * Duplicate Entry - 207
     * Unauthorized Access - 401
     * Not Found - 404
     * Insufficient Fund - 402
     * Success - 200
     * 
     * API RESPONSE CODE:
     * 00 - Success
     * 01 - Access denied
     * 02 - Insufficient fund
     * 03 - Subscription expired
     * 04 - Invalid request
     * 05 - Failed request
     * 06 - Inactive Service
     */

    public function customResp($statusCode, $responseCode, $responseMessage, $resultsData = "") {

        http_response_code($statusCode);

        echo json_encode([

            "resposeCode"  => "$responseCode",
            
            "message" => $responseMessage,

            "data" => $resultsData

        ]);

    }

}
?>