<?php

/* 
 * author:    Thanh Pham
 * purpose:   accept POST parameter fields
 *            send to email service
 *                if service fails, 
 *                    use other service
 * response:  JSON
 * date:      July 6, 2014
 * 
 */

require_once('../helpers/SystemHelper.php');

class mailerController extends SystemHelper {

    private $private_key = '573f1ba65c9dce76a856c206eb8445c0c5e71a45';

    public function __construct() { 
        try {
            $this->_email_data = @filter_var($_POST, FILTER_SANITIZE_STRING);
        } catch(Exception $e) { }
        
        if(!empty($this->_email_data)){
            try {
                $this->key         = @filter_var($_POST['key'], FILTER_SANITIZE_STRING);        
            } catch(Exception $e) { }            
        }

    }
    
    //if key matches, try to send email via mailgun, else mandrill
    public function send() {

         // Mailer only accepts POST requests
         if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'){
             
            if($this->key !== $this->private_key ) {

                    $status = array('status' => 400, 'result' => 'Missing the required field to use the service');              
                    print_r(json_encode($status));
                    return(json_encode($status)); //unit tests
                    exit;
            }             

            $form_data = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            //check if incoming params are correct
            require_once('../helpers/Data.php');

            $email_data_fields = new data($form_data);
            $required_params   = $email_data_fields->cleaner(); 
            
            header('Content-Type: application/json; charset=utf-8');                    

            if( isset($required_params->error['error']) || ($required_params === false) ) {
                $status = array('status' => 400, 'result' => 'Missing or malformed required fields (from, from_email, to, to_email, subject, text)');
                print_r(json_encode($status));

            } else {

                //try mailgun
                try{
                    $result = $this->call_mailgun($email_data_fields);
                } catch (Exception $ex) { }
                
                //failed, try mandrill
                if( isset($result['error']) || $ex ){ 
                    $man_result = $this->call_mandrill($email_data_fields);

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
                }

            }

        } else { 
                       
            $status = array('status' => 405, 'result' => "Wrong HTTP method(" . $_SERVER['REQUEST_METHOD'] . ")");            
            print_r(json_encode($status));        
            return(json_encode($status)); //unit tests
            
        }  

    }
    
    //mailgun call requires cleaned email_data
    protected function call_mailgun($email_data){ 
        $email_service = null;
        $s             = null;
        
        require_once('../sources/mailgun.php');
        $email_service     = new mailgun($email_data);
        $s = ($email_service->send());
        return $s;
    }

    //mandrill call requires cleaned email_data
    protected function call_mandrill($email_data){
        $email_service = null;
        $s             = null;

        require_once('../sources/mandrill.php');
        $email_service     = new mandrill($email_data);
        $s = ($email_service->send());
        return $s;
    }    
  
}