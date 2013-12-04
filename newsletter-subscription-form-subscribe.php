<?php

require_once ( __DIR__ . '/inc/request.php' );
require_once ( __DIR__ . '/inc/api.php' );

$user_key = $_POST['user_key']; unset($_POST['user_key']);
$list_id = $_POST['list_id']; unset($_POST['list_id']);
$email = $_POST['email']; unset($_POST['email']);

$params = array(
    'user_key' => $user_key,
    'list_id' => $list_id,
    'email' => $email,
    'data' => $_POST
);

try{
    NewsletterAPI::call('/List/SubscribeEmail/', $params);
    echo 1;   
}
catch(exception $e){
    echo $e;
}
