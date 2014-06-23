<?php
//if(!defined('ACCESS') ) { die('permission denied');}

/* 
 * author:    Thanh Pham
 * purpose:   accept POST parameter fields
 *            send to email service
 *                if service fails, 
 *                    use other service
 * 
 */

//test data
$email_data  = array('from'       => 'Thanh Pham',
                     'from_email' => 'artofguitar@gmail.com',
                     'to'         => 'Tester',
                     'to_email'   => 'artofguitar+1@gmail.com',
                     'subject'    => 'Just seeing...',
                     'text'       => 'this is my body, there are many like it but this one is mine.');



    // Mailer only accepts POST requests
//     if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'){
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'GET'){
        define('ACCESS', true);                     //access to other classes
        require_once('../helpers/Data.php');
        $result            = null;
        $status            = null;
        $email_data_fields = null;
        $required_params   = null;
        
        //check if incoming params are correct
        $email_data_fields = new data($email_data);
        $required_params   = $email_data_fields->cleaner(); 
        
        if( isset($required_params->error['error']) ) {
            $status = array('status' => 400, 'result' => 'Missing required fields (from, from_email, to, to_email, subject, text)');
            print_r(json_encode($status));
//            return(json_encode($status) );      
            
        } else { 
            //try mailgun
            $result = call_mailgun($email_data_fields);

            //failed, try mandrill
            if( isset($result['error']) ){  
                $man_result = call_mandrill($email_data_fields);
                
                if( isset($man_result['error']) ) {
                    print_r( json_encode($man_result) );
                } else { //worked
                    print_r( json_encode($man_result) );                    
                }
                
            } else {
                print_r( json_encode($result) );
//                return $result;
            }
                
        }
        
        //try to send with MailGun first, if it fails, try Mandrill

        if($result === false) die('false');
        
        
    } else {
        define('ACCESS', false);                     //access to other classes        
        $status = array('status' => 405, 'result' => "Wrong HTTP method(" . $_SERVER['REQUEST_METHOD'] . ")");
        print_r(json_encode($status));        
        exit();
    }  
    
    //mailgun call requires cleaned email_data
    function call_mailgun($email_data){
        $email_service = null;
        $s             = null;
        
        require_once('../sources/mailgun.php');
        $email_service     = new mailgun($email_data);
        $s = ($email_service->send());
        return $s;
    }
    
    //mandrill call requires cleaned email_data
    function call_mandrill($email_data){
        $email_service = null;
        $s             = null;
        
        require_once('../sources/mandrill.php');
        $email_service     = new mandrill($email_data);
        $s = ($email_service->send());
        return $s;
    }     