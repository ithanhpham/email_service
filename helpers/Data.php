<?php
if(!defined('ACCESS') ) { die('permission denied');}

/* 
 * author:  Thanh Pham
 * purpose: clean incoming data from forms to service
 * params:  constructor needs the $email_data 
 */

require_once('SystemHelper.php');

    class data {
        
        public function __construct($email_data = false) {
            $this->_email_data = $email_data;
            $this->system = new SystemHelper;            
        }
        
        //clean the data and assemble the to and from fields
        public function cleaner() {

            //check for the correct number of params
            if(sizeof($this->_email_data) !== 6) {
                
                $this->system->get_status_code(400, 'Bad Request: often a missing or empty parameters(email)');
//                exit();      
                  return $this->system;
            }
            
            //parse the array and trim it; check if the trimmed value is empty
            foreach($this->_email_data as $key => $val){
                
                trim($val);
                
                if( empty($val) === false ){
                    $this->_email_data[$key] = trim($val);
                    
                } else {
                    $this->system->get_status_code(400, 'Bad Request: often a missing or empty parameters');
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

                } else {
                    $this->system->get_status_code(400, 'Bad Request: often a missing or empty parameters(email)');
                    return false;
                }
                
            } else {
                $this->system->get_status_code(400, 'Bad Request: often a missing or empty parameters(email)');
                return false;                                           
            }
            
            return true;
        }        

    }