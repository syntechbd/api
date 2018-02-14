<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commonlib {
    
    public $obj = null;
    
    public function __construct()
    {
        $this->obj =& get_instance();
    }
    
    function generateRandomString($length = 10) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $chars_length = strlen($chars);
        $str = '';
        for ($i=0; $i<$length; $i++) {
            $str .= $chars[rand(0, $chars_length - 1)];
        }

        return $str;
    }
    
    function isPost(){
        return $this->obj->input->server('REQUEST_METHOD') == 'POST' ? TRUE : FALSE;
    }
    
    function isGet(){
        return $this->obj->input->server('REQUEST_METHOD') == 'GET' ? TRUE : FALSE;
    }
    
    ## Trims Array. Supports multidimensional array
    function trim_array($arr) {
        array_walk_recursive($arr, function (&$trma) {
            $trma = trim($trma);
        });
        return $arr;
    }
    
    ## Trims Array and removes TAG. Supports multidimensional array
    function trimArrayRemoveTag($arr){
        array_walk_recursive($arr, function (&$trma) {
            $trma = strip_tags(trim($trma));
        });
        return $arr;
    }
    
    public function toNumeric($str){
        return is_numeric($str) ? sprintf('%g', floatval($str)) : 0;
    }
    
    ## encodes an array into a string
    function encode_array($arr) {
        return base64_encode(serialize($arr));
    }

    ## Return-backs as array from encoded string
    function decode_array($str) {
        return unserialize(base64_decode($str));
    }
    
    ## TODO:: would be modified later on
    function encrypt_srting($text){
        return $text;
        //return base64_encode($text);
        //return urlencode(base64_encode($text));
        //return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->obj->config->item('salt'), $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
    }

    ## TODO:: would be modified later on
    function decrypt_srting($text){
        return $text;
        //return base64_decode($text);
        //return base64_decode(urldecode($text));
        //return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->obj->config->item('salt'), base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
    }
    
    function isValidDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    function isFutureDate($date) {
        $opening_date = new DateTime($date);
        $current_date = new DateTime();
        return $opening_date > $current_date ? true : false;
    }
    
    public function apiRequest($url, $params=[], $type='get'){
        
        $this->obj->config->load('config_api');
        
        $username   = $this->obj->config->item('api_request_username');
        $password   = $this->obj->config->item('api_request_password');
        $headers = array(
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Host'          => 'sms.posspot.com'
        );
        
        $options = array(
            CURLOPT_HTTPAUTH            => CURLAUTH_BASIC,
            CURLOPT_USERPWD             => "{$username}:{$password}",
            CURLOPT_SSLVERSION          => 3,
            CURLOPT_CONNECTTIMEOUT 		=> 3000,
            CURLOPT_AUTOREFERER 		=> true,
            CURLOPT_RETURNTRANSFER 		=> true,            
            CURLOPT_FOLLOWLOCATION 		=> true,
            CURLOPT_COOKIESESSION       => true,
            CURLOPT_SSL_VERIFYPEER 		=> false,
            CURLOPT_SSL_VERIFYHOST 		=> 0,
            CURLOPT_ENCODING            => "UTF-8",
            CURLOPT_HTTPHEADER          => $headers,
            //CURLOPT_HEADER              => true,
        );
        if($type == 'post'){
            $options[CURLOPT_POST] = TRUE;
            $options[CURLOPT_POSTFIELDS] = $params;
        }
        
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }
}
