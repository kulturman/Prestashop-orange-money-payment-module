<?php
if(!function_exists('sendOrangeMoneyPayment')) {
    function sendOrangeMoneyPayment($customerNumber, $amount, $otp) {
        $externalTransactionId = $customerNumber .(int)$amount . rand(1,99999) . Configuration::get('ORANGE_MONEY_MERCHANT_NUMBER');
        
        $params = '
            <?xml version="1.0" encoding="UTF-8"?>
            <COMMAND>
                <TYPE>OMPREQ</TYPE>
                <customer_msisdn>'.$customerNumber.'</customer_msisdn>
                <merchant_msisdn>'.Configuration::get('ORANGE_MONEY_MERCHANT_NUMBER').'</merchant_msisdn>
                <api_username>'.Configuration::get('ORANGE_MONEY_MERCHANT_ID').'</api_username>
                <api_password>'.Configuration::get('ORANGE_MONEY_MERCHANT_PASSWORD').'</api_password>
                <amount>'.$amount.'</amount>
                <PROVIDER>101</PROVIDER>
                <PROVIDER2>101</PROVIDER2>
                <PAYID>12</PAYID>
                <PAYID2>12</PAYID2>
                <otp>'.$otp.'</otp>
                <ext_txn_id>'.$externalTransactionId.'</ext_txn_id>
            </COMMAND>
        ';
        $url = Configuration::get('ORANGE_MONEY_IS_TEST_MODE') == 1
            ? "https://testom.orange.bf"
            : "https://apiom.orange.bf";
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_POSTFIELDS, $params);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        $response = '<response>'.$response.'</response>';
        $xml = simplexml_load_string($response);
        $json = json_decode(json_encode($xml));
        return $json;
    }
}
