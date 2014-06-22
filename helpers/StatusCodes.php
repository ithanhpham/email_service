<?php

/* helper method for status codes */

class StatusCodes {
    
    public function get($en, $em) {
        
        if(isset($en) && isset($em) ){
            
            if($en >= 200 && $en < 300) {
                $this->status = array('status' => $en, 'result' => $em);
                print_r(json_encode($this->status));
                
            } else {
                $this->error  = array('error' => $en, 'result' => $em);
                print_r(json_encode($this->error));                    
            }
        }            
    }    
}