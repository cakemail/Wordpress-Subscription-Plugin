<?php

class NewsletterAPI {
 
    private static $apiUrl = 'https://publicapi.wbsrvc.com';
    
    public static function call($url, $params) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, self::$apiUrl . $url); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $result = curl_exec($ch);
        
        if ($result === false) {
        
            throw new Exception('Curl error: ' . curl_error($ch));
        
        } else {
        
            if (!$result = json_decode($result)) {
                throw new Exception('Error connecting API');
            } 
            
            if ($result->status != 'success') {
                throw new Exception($result->data);
            }
        
        }
        
        curl_close($ch);
        return $result->data;
    }

    public static function getLogin($username, $password){
        $params = array(
            'email'    => $username,
            'password' => $password
        );

        try
        {
            return NewsletterAPI::call('/User/Login/', $params);
        }
        catch (Exception $e)
        {
            $instance['registered'] = false;

            if ( strpos( $e->getMessage(), __('The combination email-password does not exists!','newsletter-subscription-widget') ) !== false ){ $instance['error_code'] = 1; }
            elseif ( strpos( $e->getMessage(), __('The combination email-password does not exists!','newsletter-subscription-widget') ) !== false ){ $instance['error_code'] = 2; }
            else{ $instance['error_code'] = 99; $instance['error_message'] = $e->getMessage(); }

            return false;
        }
    }

    public static function getUser($user_key, $user_id){
        $params = array(
            'user_key' => $user_key,
            'user_id' => $user_id
        );

        try
        {
            return NewsletterAPI::call('/User/GetInfo/', $params);
        }
        catch(exception $e)
        {
            $instance['error_code'] = 99;
            $instance['error_message'] = $e->getMessage();

            return false;
        }
    }

    public static function getLists($user_key){
        $params = array(
            'user_key' => $user_key
        );

        try
        {
            return NewsletterAPI::call('/List/GetList/', $params);
        }
        catch(exception $e)
        {
            $instance['error_code'] = 99;
            $instance['error_message'] = $e->getMessage();

            return false;
        }
    }

    public static function createList($params){
        try
        {
            return NewsletterAPI::call('/List/Create/', $params);
        }
        catch(exception $e)
        {
            $instance['error_code'] = 99;
            $instance['error_message'] = $e->getMessage();

            return false;
        }        
    }

    public static function getClient($user_key){
        $params = array(
            'user_key' => $user_key
        );

        try
        {
            return NewsletterAPI::call('/Client/GetInfo/', $params);
        }
        catch(exception $e)
        {
            $instance['error_code'] = 99;
            $instance['error_message'] = $e->getMessage();

            return false;
        }
    }

    public static function getFields($user_key, $list_id){
        $params = array(
            'user_key' => $user_key,
            'list_id' => $list_id
        );

        try
        {
            return NewsletterAPI::call('/List/GetFields/', $params);
        }
        catch(exception $e)
        {
            $instance['error_code'] = 99;
            $instance['error_message'] = $e->getMessage();

            return false;
        }
    }
}
