<?php

/* 
 * Author:  Thanh Pham
 * purpose: PHP posting mailer tester
 */
if($_SERVER['HTTP_HOST'] == 'thanhsguitar.com') {
    $url = 'http://thanhsguitar.com/projects/email_service/controller/mailer?entry=f4S!nd3FDs'; 
} else { //local
    $url = 'http://localhost:8888/email_service/controller/mailer?entry=f4S!nd3FDs';    
}
    
$email_data  = array('from'       => 'Thanh Pham',
                     'from_email' => 'artofguitar@gmail.com',
                     'to'         => 'Tester',
                     'to_email'   => 'artofguitar+1@gmail.com',
                     'subject'    => time() . ' Just seeing...',
                     'text'       => 'if this emailer is going to jump over the lazy fox.');

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($email_data),
    ),
);

$context  = stream_context_create($options);

//try using file_get_contents
//try {
//    $result = @file_get_contents($url, false, $context);
//    $result = json_decode($result);
//    if($result == null) {
//        $result =  '"status", "fail"';
//        $result = json_decode($result);
//    }
//} catch (Exception $ex) {
//    $result->status = 'error';
//}

//try to cURL it if $result is NULL
//if($result == null) {
if(1 === 1){    
    try {
        $curl_url = $url;

        if(filter_var($curl_url, FILTER_VALIDATE_URL) ){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $curl_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $email_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                     
            $c_r = curl_exec($ch);                 
            curl_close($ch);

            $result = json_decode($c_r);   

            }

    } catch (Exception $ex) {
        $result->status = 'failed';
        die('caught exception');
    }
    if(empty($result))     {
        $result['status'] = 'failed';
    }
} 


if($result->status === 200) {
    print_r(json_encode($result));    
} else {
    $status = array('status' => 'error', 'result' => 'the service failed. Try a different method of calling the service.');
    print_r(json_encode($status));
             
}

unset($result);