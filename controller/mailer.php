<?php

/* 
 * author:    Thanh Pham
 * purpose:   accept POST parameter fields
 *            send to email service
 *                if service fails, 
 *                    use other service
 * response:  JSON
 * date:      June 23, 2014
 * 
 */
            
    // Mailer only accepts POST requests
     if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'){
        define('ACCESS', true);                     //access to other classes

        $form_data = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        
        //check if incoming params are correct
        require_once('../helpers/Data.php');
        
        $email_data_fields = new data($form_data);
        $required_params   = $email_data_fields->cleaner(); 
        
        if( isset($required_params->error['error']) ) {
            $status = array('status' => 400, 'result' => 'Missing required fields (from, from_email, to, to_email, subject, text)');
            print_r(json_encode($status));
            
        } else {

            //try mailgun
            $result = call_mailgun($email_data_fields);

            //failed, try mandrill
            if( isset($result['error']) ){  
                $man_result = call_mandrill($email_data_fields);

                //failed, both methods failed
                if( isset($man_result['error']) ) {
                    $status = array('status' => 400, 'result' => array("mailgun and mandrill failed" => 
                              array('mailgun' => $result, 
                                    'mandrill' => $man_result)) );
                    
                    print_r( json_encode($status) );

                } else { //worked
                    print_r( json_encode($man_result) );                    
                }
                
            } else {
                print_r( json_encode($result) );
//                return $result;
            }
                
        }
                   
    } else {
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