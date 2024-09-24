<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Confirm_expedisi_model extends CI_Model {

    
    public function __construct()
    {
        parent::__construct();
    }

    public function api($url=null, $data=NULL)
    {
        $curl = curl_init();
        $murl = 'http://localhost/api/'.$url;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $murl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

}

/* End of file Confirm_expedisi_model.php */
