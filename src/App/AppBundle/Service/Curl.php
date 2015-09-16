<?php

namespace App\AppBundle\Service;

class Curl {
    
    /**
     * 
     * @param type $url
     * @param array $postArray optional
     * @return string content as string
     */
    public function send($url, array $postArray = array(), $additionalCurlFlags = array()) {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
//            curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
            curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_ENCODING, "UTF-8" );
            if(!empty($postArray)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postArray));
            }
            if(!empty($additionalCurlFlags)) {
                foreach($additionalCurlFlags as $flag => $value) {
                    curl_setopt($ch,$flag,$value);
                }
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $response = curl_exec($ch);
//            var_dump(curl_getinfo($ch));
//            var_dump($response);
            if (FALSE === $response) {
                throw new \Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
        }catch(\Exception $e) {
//            var_dump($e->getCode(), $e->getMessage());
            return null;
        }
        return $response;
    }
    
}