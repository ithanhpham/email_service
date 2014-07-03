<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Thanh Pham Email Service info</title>
    </head>
    <body>

        <h1>Email service</h1>
        <h2>project</h2> 
        <p>This is an email service that is called to send out email. One of the strengths of this service is that if a dependency on 3rd party service fails,
            the service will try to use a different email api. The main email service is MailGun and the backup is Mandrill.</p>
        <h2>application track</h2>
        <p>backend developer</p>

        <h2>Reason why</h2>
        <p>I chose the email service because I enjoy writing backend code, have some experience using mailgun in my current job's dev portal  and it's 
            something that can be re-used at some other point. I used PHP as the backend because I am most familiar with that language. 
            I have 2.5+ years with it. My QA background always has me thinking about use cases which helps with the quality of the application.</p>

        <h2>trade-offs/more info</h2>
        The usual trade-offs of speed vs quality vs price comes into play. Due to time constraints, I wasn't able to incorporate this into a framework like CodeIgniter to give it a MVC framework. This would allow routing to be seemless and less time on writing sanitization code.

Like with any API that we write at my current company, I would like to write unit tests. This would especially be important for all the use cases due to dependancies on the third parties. Getting this deployed on Amazon was also a challenge. Again, this is due to time constraints. With more time, I'd like to have a nice interface test out the service.

<h2>logic</h2>


<ol>the logic in the controller:
    <li>check if it is a POST request</li>
    <li>check all params</li>
    <li>try to use mail gun to send email</li>
    <li>if fails, use Mandrill to send email</li>
    <li>fails if both services fail</li>
    <li>returns JSON</li>        
</ol>
    
<h2>Example:</h2> 

<blockquote><div class='code' style="font-family: helvetica, arial, sans-serif">curl -X POST 'https://thanh-email-service.herokuapp.com/email_service/controller/mailer.php' -d "from=from name" -d "from_email=EMAIL" -d "to=to name" -d "to_email=EMAIL" -d "subject=sweet subject" -d "text=" -d "key=KEY" </div></blockquote>

    <li>the email requires a POST</li>
    
    <li>if POST, you'll need the following data fields: from, from_email, to, to_email, subject, text, key (use the one in the code for now).

    <li><a href='http://thanh-email-service.herokuapp.com/form_test.php'>Email form</a>(send only)</li>

    <li>PHP example of a curl: 
        
        <ul>
            <pre>
        $email_data  = array(
                     'from'               => 'Joe Schmoe',
                     'from_email'         => 'user@email.ext',
                     'to'                 => 'Jane Smith',
                     'to_email'           => 'janiers@domain.ext',
                     'subject'            => Email  with me',
                     'text'               => some email magic is about to happen.... look out',
                     'key'                => 'yourkey');

        $options = array(
            'https' => array(
                            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method'  => 'POST',
                            'content' => http_build_query($email_data),
            ),
        );

        $context  = stream_context_create($options);
        
        try {
            $curl_url = $url;

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

        } catch (Exception $ex) {
        //handle the exception
        }
            </pre>
        </ul>
        
    </li>

    </body>
</html>
