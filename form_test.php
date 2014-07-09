<?php 
session_start();

date_default_timezone_set("UTC");      

//which server to use?
function service_domain(){
    
    switch ($_SERVER['HTTP_HOST']) {    
    
        case 'thanhsguitar.com':
            define('DOMAIN', 'http://thanhsguitar.com');
            $url = 'http://thanhsguitar.com/projects/email_service/controller/mailer'; 
            break;

        case 'thanh-email-service.herokuapp.com':
            define('DOMAIN', 'http://thanh-email-service.herokuapp.com');            
            $url = 'http://thanh-email-service.herokuapp.com/controller/mailer'; 
            break;

        default:
            define('DOMAIN', 'http://localhost:8888/email_service');                        
            $url = 'http://localhost:8888/email_service/controller/mailer';    
            break;
        }

        return $url;
}

//if data is sent from the form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    
    if( isset($_GET['form_status']) && ($_GET['form_status'] == 'thanks') ){

        //robot?
        if(!empty($_POST['address'])){
            header('Location: form_test.php');    
            
        } elseif( !isset($_SESSION['address2']) ||  ($_SESSION['address2'] !== $_POST['address2']) ){
            
            $curl_url = service_domain();

            $email_data  = array(
                                 'from'       => filter_var(trim($_POST['from']),       FILTER_SANITIZE_STRING),
                                 'from_email' => filter_var(trim($_POST['from_email']), FILTER_SANITIZE_EMAIL),
                                 'to'         => filter_var(trim($_POST['to']),         FILTER_SANITIZE_STRING),
                                 'to_email'   => filter_var(trim($_POST['to_email']),   FILTER_SANITIZE_EMAIL),
                                 'subject'    => filter_var(trim($_POST['subject']),    FILTER_SANITIZE_STRING),
                                 'text'       => filter_var(trim($_POST['text']),       FILTER_SANITIZE_STRING),
                                 'key'        => '573f1ba65c9dce76a856c206eb8445c0c5e71a45'
                                );

            try {
                if( filter_var(trim($curl_url, FILTER_VALIDATE_URL)) ){ 
                    $_POST['form_used'] = 'thank_you'; //prevent page from executing call again

                    $ch  = curl_init();
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_URL, $curl_url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $email_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $c_r = curl_exec($ch);
                    curl_close($ch);

                    $result = json_decode($c_r);
                    
                    $_SESSION['address2'] = $_POST['address2'];

                    if(!empty($result)) {
                        
                        print_r(json_encode($result));    
                        exit;                        
                    } else {
                        
                        die('Sorry! Error calling the service.');
                    }

                }
            } catch (Exception $ex) {
                $result->status = 'failed';
                $result->result = 'could not execute the mailer call';
                exit;
            }
        
        } 
        else {
            header("Location: form_test.php");
            session_destroy();
            exit;
        } // end of elseif( !isset($_SESSION['address2']) || ~ line 39
        
    } else {
        die('error page invalid.');
    }
    
    
} else {
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>ACME email form</title>
<link rel="stylesheet" type="text/css" href="form_test/view.css" media="all">
<script type="text/javascript" src="form_test/view.js"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-28096092-1', 'auto');
  ga('send', 'pageview');

</script>
</head>
<body id="main_body" >

	<img id="top" src="form_test/top.png" alt="">
	<div id="form_container">
	
		<h1><a>ACME email form</a></h1>
		<form id="form_867176" class="appnitro"  method="post" action="form_test.php?form_status=thanks">
					<div class="form_description">
			<h2>ACME email form</h2>
			<p>Email test form</p>
		</div>						
			<ul >
			
					<li id="li_3" >
		<label class="description" for="element_3">Who is this going to? </label>
		<span>
			<input id="to" name= "to" class="element text" maxlength="255" size="8" value=""/>
			<label>Name</label>
		</span>
		</li>		<li id="li_2" >
		<label class="description" for="element_2">What is their email? </label>
		<div>
			<input id="to_email" name="to_email" class="element text medium" type="text" maxlength="255" value=""/> 
		</div> 
		</li>		<li id="li_4" >
		<label class="description" for="element_4">What is the subject? </label>
		<div>
			<input id="subject" name="subject" class="element text medium" type="text" maxlength="255" value=""/> 
		</div> 
		</li>		<li id="li_1" >
		<label class="description" for="element_1">What do you want to say? </label>
		<div>
			<textarea id="text" name="text" class="element textarea medium"></textarea> 
		</div><p class="guidelines" id="guide_1"><small>Message body</small></p> 
		</li>
                <input type="hidden" name="from" value="email tester"></input>
                <input type="hidden" name="from_email" value="no-reply@emailtester.com"></input>
                <input type="hidden" name="address" value=""></input>
                <input type="hidden" name="address2" value="<?=  md5(time()); ?>"></input>
                    
                
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="867176" />
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
			Demo form to test the email service
		</div>
	</div>
	<img id="bottom" src="form_test/bottom.png" alt="">
	</body>
</html>

<?php } ?>
