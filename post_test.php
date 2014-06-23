<?php

/* 
 * Author:  Thanh Pham
 * purpose: test posting to mailer
 */

$url = 'http://localhost:8888/email_service/controller/mailer';
$email_data  = array('from'       => 'Thanh Pham',
                     'from_email' => 'artofguitar@gmail.com',
                     'to'         => 'Tester',
                     'to_email'   => 'artofguitar+1@gmail.com',
                     'subject'    => time() . ' Just seeing...',
                     'text'       => 'this is my body, there are many like it but this one is mine.');


$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($email_data),
    ),
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
print_r($result);
unset($result);