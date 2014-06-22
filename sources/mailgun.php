<?php

/* 
 * Author: Thanh Pham
 * usage:  call Mailgun's services to send mail with params
 * helpful urls: http://documentation.mailgun.com/
 * assume variables for mailgun will be the same
 */

require_once('../helpers/StatusCodes.php');
require_once('../helpers/Data.php');

class mailgun {
    
    private $mailgun_api_key    = 'key-296pfbwxwrygn8aejjfbjytp5yqnpji8';
    private $base_url           = 'https://api.mailgun.net/v2';
    private $domain             = 'thanhsguitar.com';
    
    public function __construct($email_data) { 

        //check if post
//        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'){
            $this->_email_data = $email_data;
            $this->status_code = new StatusCodes;
            $this->data        = new Data($this->_email_data);            
            
        } else {
            $this->status_code->get(405, $_SERVER['REQUEST_METHOD'] . '. Wrong HTTP method');
            exit();
        }
                
    }
    
    
    /*
     * method to send mailgun message 
     * param: data array that contains
        $email_data  = array('from'       => 'Thanh Pham',
                             'from_email' => 'nylonthanh@yahoo.com',
                             'to'         => 'Tester',
                             'to_email'   => 'thanh.pham@yahoo.com',
                             'subject'    => 'Just seeing...',
                             'text'       => 'this is my body, there are many like it but this one is mine.');     
     */

   /* additional items to add:
    -F cc='bar@example.com' \
    -F bcc='baz@example.com' \
    -F subject='Hello' \
    -F text='Testing some Mailgun awesomness!' \
    --form-string html='<html>HTML version of the body</html>' \
    -F attachment=@files/cartman.jpg \
    -F attachment=@files/cartman.png
     */
    public function send(){ 

        $curl_url          = null;
        $clean_status      = $this->data->cleaner();
        $this->_email_data = $this->data->_email_data;

        if($clean_status === true) { 
          
            try {
              
                //create string to curl
                $curl_url = $this->base_url . '/' . $this->domain . '/messages';
                
                if(filter_var($curl_url, FILTER_VALIDATE_URL) ){
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, "api:" . $this->mailgun_api_key);
                    curl_setopt($ch, CURLOPT_URL, $curl_url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_email_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                     

                    $c_r = curl_exec($ch);                 
                    curl_close($ch);

                    if( strpos($c_r, 'Queued') !== false) {
                        $this->status_code->get(200, json_decode($c_r) );                    
                        exit();
                        
                    } else {
                        $this->status_code->get(400, "Client Error: $c_r" );
                        exit();
                    }
                                        
                  } else {
                      $this->status_code->get(400, 'Invalid service URL');
                      exit();                    
                  }

                
            } catch(Exception $e) {
              var_dump($e);
            }

            
        } else {       
          return false;
          
        }
    }
    
}

//test data
$email_data  = array('from'       => 'Thanh Pham',
                     'from_email' => 'thanh@thismoment.com',
                     'to'         => 'Tester',
                     'to_email'   => 'thanh+emailtest@thismoment.com',
                     'subject'    => 'Just seeing...',
                     'text'       => 'this is my body, there are many like it but this one is mine.');


$mail = new mailgun($email_data); 
$mail->send();
