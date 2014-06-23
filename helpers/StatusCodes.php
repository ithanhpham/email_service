<?php
if(!defined('ACCESS') ) { die('permission denied');}

/* helper method for status codes */

class StatusCodes {
    
    public function get($en, $em, $source = null) {
        
        if(isset($en) && isset($em) ){
            
            if(isset($source)) {
                if($en >= 200 && $en < 300) {
                    $this->status = array('status' => $en, 'source' => $source, 'result' => $em);
                    return($this->status);

                } else {
                    $this->error  = array('error' => $en, 'source' => $source, 'result' => $em);
                    return($this->error);
                }                
            } else {
                if($en >= 200 && $en < 300) {
                    $this->status = array('status' => $en, 'result' => $em);
                    return($this->status);

                } else {
                    $this->error  = array('error' => $en, 'result' => $em);
                    return($this->error);
                }                               
            }

        }            
    }    
}