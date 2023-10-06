<?php

namespace vebinarClass\messageQueue\Handlers;

class Smsc{

    private $url = 'https://smsc.ru/sys/send.php';
    private $login = 'fluidline';
    private $password = '9c3vMf4xABcDyHb';

    public function sendMessage($contact, $text)
    {
        $query_params = http_build_query(array(
            'login' => $this->login,
            'psw' => $this->password,
            'phones' => $contact,
            'mes' => $text,
        ));

        $url = $this->url . '?' . $query_params;

        // GET query to API endpoint
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            $err_message = curl_error($ch);
            curl_close($ch);
            throw new \Exception($err_message);
        }

        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200){
            curl_close($ch);
            throw new \Exception($response);
        }

        curl_close($ch);

        // Smsc API returns error messages in response, not by error response
        if(strpos($response, 'ERROR') !== false){
            throw new \Exception($response);
        }
    }
    
}