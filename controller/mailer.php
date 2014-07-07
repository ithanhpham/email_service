<?php

/* 
 * This will call the mailer class
 */
require_once('mailerController.php');

define('ACCESS', true);                     //access to other classes

require_once('mailerController.php');

$postData = filter_var($_POST, FILTER_SANITIZE_STRING);
$sendMail = new mailerController($postData);

return @$sendMail->send();