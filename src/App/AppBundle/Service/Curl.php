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
    
    public function curlMultiSend($data, $options = array()) {
 
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

          $curly[$id] = curl_init();

          $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
          curl_setopt($curly[$id], CURLOPT_URL,            $url);
          curl_setopt($curly[$id], CURLOPT_HEADER,         0);
          curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

          // post?
          if (is_array($d)) {
            if (!empty($d['post'])) {
              curl_setopt($curly[$id], CURLOPT_POST,       1);
              curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
            }
          }

          // extra options?
          if (!empty($options)) {
            curl_setopt_array($curly[$id], $options);
          }

          curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
          curl_multi_exec($mh, $running);
        } while($running > 0);


        // get content and remove handles
        foreach($curly as $id => $c) {
          $result[$id] = curl_multi_getcontent($c);
          curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
      }
    
    
}