<?php

define('ACCESS', true);                     //access to other classes

if(!isset($_SERVER['REQUEST_URI'])){
    
    require_once('../helpers/SystemHelper.php');    
    require_once('../controller/mailer.php');    
} else {
    
    require_once('helpers/SystemHelper.php');    
    require_once('controller/mailer.php');
    
}


class mailerTest extends PHPUnit_Framework_TestCase {
//
    public function __construct(){
        $this->emailerClass = new mailer();          
    }
    //test the controller
    //this should calling the class w/o params should be false
    public function testEmptyParams(){
        
        //constructor params should not be populated
        $result1 = $this->assertFalse($this->emailerClass->_email_data);
        $result2 = json_decode( $this->emailerClass->send() );
        
        $this->assertTrue(empty($this->emailerClass->key));

        //must be a post 
        $this->assertTrue($result2->status == 405 );
        
        $this->assertContains('Wrong HTTP method', $result2->result);               
                
        return $this->emailerClass;
        
    }
    
    //test using curl
    public function testPost(){
        //build URL to curl
        $curl_url = SystemHelper::service_domain();

        if(filter_var($curl_url, FILTER_VALIDATE_URL) ){ 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $curl_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $email_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                     
            $c_r = curl_exec($ch);                 
            curl_close($ch);

            $result = json_decode($c_r);   

        }
        
        //should return something
        $this->assertTrue(isset($result));
        
        //json object should have status and result
        $this->assertObjectHasAttribute('status',  $result);
        
        $this->assertObjectHasAttribute('result',  $result);
        
        //should be a 400 since we didn't pass anything
        $this->assertTrue($result->status === 400);
        
        $this->assertContains('Missing the required field to use the service', $result->result);
        
    }
    
}