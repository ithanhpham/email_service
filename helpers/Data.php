<?php
if(!defined('ACCESS') ) { die('permission denied');}

/* clean incoming data from forms to service */

require_once('StatusCodes.php');

    class data {
        
        public function __construct($email_data) {
            $this->_email_data = $email_data;
            $this->status_code = new StatusCodes;            
        }
        
        //clean the data and assemble the to and from fields
        public function cleaner() {

            //check for the correct number of params
            if(sizeof($this->_email_data) !== 6) {
                
                $this->status_code->get(400, 'Bad Request: often a missing or empty parameters(email)');
//                exit();      
                  return $this->status_code;
            }
            
            //parse the array and trim it; check if the trimmed value is empty
            foreach($this->_email_data as $key => $val){
                
                trim($val);
                
                if( empty($val) === false ){
                    $this->_email_data[$key] = trim($val);
                    
                } else {
                    $this->status_code->get(400, 'Bad Request: often a missing or empty parameters');
                    return false;                               
                }
            }
            
            //check if emails are valid
            if(filter_var($this->_email_data['from_email'], FILTER_VALIDATE_EMAIL) && 
               filter_var($this->_email_data['to_email'],   FILTER_VALIDATE_EMAIL) ) 
            {

                //sanitize
                $args = array('from'       => FILTER_SANITIZE_STRING,
                              'from_email' => FILTER_SANITIZE_EMAIL,
                              'to'         => FILTER_SANITIZE_STRING,
                              'to_email'   => FILTER_SANITIZE_EMAIL,
                              'subject'    => FILTER_SANITIZE_STRING,
                              'text'       => FILTER_SANITIZE_STRING 
                );

                if( sizeof(filter_var_array($this->_email_data, $args) ) === 6){

                    //build the from and to

//                    $this->_email_data['from'] = $this->_email_data['from'] . " &lt" . $this->_email_data['from_email'] . "&gt";
//                    $this->_email_data['to']   = $this->_email_data['to'] . " &lt" . $this->_email_data['to_email'] . "&gt";                    
                    
                    // unset($this->_email_data['from_email'], $this->_email_data['to_email']);  

                } else {
                    $this->status_code->get(400, 'Bad Request: often a missing or empty parameters(email)');
                    return false;
                }
                
            } else {
                $this->status_code->get(400, 'Bad Request: often a missing or empty parameters(email)');
                return false;                                           
            }
            
            return true;
        }        
        
        //for header injection cases, arguments: reference array
        public function sanitize(&$array) {

            foreach($array as &$data) {
                $data = str_replace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($data)); 
            }
        }


    }