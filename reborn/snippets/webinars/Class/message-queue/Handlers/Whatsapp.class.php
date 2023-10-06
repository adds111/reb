<?php

namespace vebinarClass\messageQueue\Handlers;

class WhatsApp{

    private $url = 'https://chatter.salebot.pro/api/d84cab71ac0f6f9ebcb329c24031f5fc/whatsapp_message';

    public function sendMessage($contact, $text)
    {
        $query = array(
            'phone' => $contact,
            'text' => $text,
        );

        // POST query to API endpoint
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
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

        // Sending delay because WhatsApp rate limiting
        sleep(2);
    }

}