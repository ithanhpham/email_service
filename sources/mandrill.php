<?php

/* 
 * Author: Thanh Pham
 * usage:  call mandrill's services to send mail with params
 * helpful urls: https://mandrillapp.com/api/docs/
 * 
 */

require_once('../helpers/StatusCodes.php');
require_once('../helpers/Data.php');

class mandrill {
    
    private $mandrill_api_key   = 'Jw4Q-9YgxCNIVXyxsOfVFw';
    private $base_url           = 'https://mandrillapp.com/api/1.0/';
    private $domain             = 'thanhsguitar.com';
    
    
    public function __construct($email_data) { 

        //check if post
       // if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'){
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
        method to send mandrill message 
        this will clean and build the json, then cURL it
        https://mandrillapp.com/api/1.0/messages/send.json
    */
    public function send(){ 

        $curl_url          = null;
        $clean_status      = $this->data->cleaner();
        $this->_email_data = $this->data->_email_data; 
        $json_status       = $this->process_to_json();

        if($clean_status === true && $json_status === true) { 
          
            try{ 
              
                //create string to curl
                $curl_url = $this->base_url . '/messages/send.json';

                if(filter_var($curl_url, FILTER_VALIDATE_URL) ){

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, "api:" . $this->mandrill_api_key);
                    curl_setopt($ch, CURLOPT_URL, $curl_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_email_data['json']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                     

                    $c_r = curl_exec($ch);                 
                    curl_close($ch);
                    
                    //standardize the json output.
                    $c_r = ltrim(trim($c_r), '[');
                    $c_r = rtrim($c_r, ']'); 
                    
                    if( strpos($c_r, '"status":"error"') === false) {
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
                          
            } catch(Exception $e){

                $this->status_code->get(400, $e->getMessage());            
                exit();
            }
            
        } else {       
            $this->status_code->get(400, 'Bad Request: often a missing or empty parameters(email)');

        }
        
    }
    
    /*
        build the json req to send to mandrill
        failure will have "error" as the status
        note: you can add "html":"html <h1>email</h1>"
    */
    protected function process_to_json() {
     
        $processed_json = '{
        "key": "' . $this->mandrill_api_key . '",
        "message": { 
            "text": "' . $this->_email_data['text'] . '",
            "subject": "'. $this->_email_data['subject'] .'",
            "from_email": "' . $this->_email_data['from_email']. '",
            "from_name": "' . $this->_email_data['from'] .'",
            "to": [
                {
                    "email": "' . $this->_email_data['to_email'] . '",
                    "name": "' . $this->_email_data['to'] . '"
                }
            ],
            "headers": {

            },
            "track_opens": true,
            "track_clicks": true,
            "auto_text": true,
            "url_strip_qs": true,
            "preserve_recipients": true,

            "merge": true,
            "global_merge_vars": [

            ],
            "merge_vars": [

            ],
            "tags": [

            ],
            "google_analytics_domains": [

            ],
            "google_analytics_campaign": "...",
            "metadata": [

            ],
            "recipient_metadata": [

            ],
            "attachments": [

            ]
        },
        "async": false
        }';

        $this->_email_data['json'] = $processed_json;
        
        return true;
    }

    
}


//test data
$email_data  = array('from'       => 'Thanh Pham',
                     'from_email' => 'artofguitar@gmail.com',
                     'to'         => 'Tester',
                     'to_email'   => 'artofguitar+emailtest@gmail.com',
                     'subject'    => 'Just seeing...',
                     'text'       => 'this is my body, there are many like it but this one is mine.');


$mail = new mandrill($email_data); 
$mail->send();