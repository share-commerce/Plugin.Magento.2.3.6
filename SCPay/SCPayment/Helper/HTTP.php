<?php
/**
* CURL POST
* @param string $url
* @param string $fields_string
* @return void
* @author ShareCommerce
*/

namespace SCPay\SCPayment\Helper;

Class HTTP {
    function post($url,$fields_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    } 
}