## README.md Outline


* email service

The purpose of this service is to send out email with a backup. One of the strengths of this service is that if a dependency on 3rd party service fails, the service will try to use a different email api. The main email service is MailGun and the backup is Mandrill.

* application track

backend developer

* reason why

I chose the email service because I enjoy writing backend code, have some experience using mailgun in my current job's dev portal and it's something that can be re-used at some other point. I used PHP as the backend because I am most familiar with that language. I have 2.5+ years with it. My QA background always has me thinking about use cases which helps with the quality of the application.

* trade-offs/more info

The usual trade-offs of speed vs quality vs price comes into play. Due to time constraints, I wasn't able to incorporate this into a framework like CodeIgniter to give it a MVC framework. This would allow routing to be seemless and less time on writing sanitization code. Like with any API that we write at my current company, I would like to write more detailed unit tests. This would especially be important for all the use cases due to dependancies on the third parties. Getting this deployed on Amazon was also a challenge. Again, this is due to time constraints. With more time, I'd like to have a nice interface test out the service.
logic

* the logic in the controller:
check if it is a POST request
check all params
try to use mail gun to send email
if fails, use Mandrill to send email
fails if both services fail
returns JSON
Example:

curl -X POST 'https://thanh-email-service.herokuapp.com/email_service/controller/mailer.php' -d "from=from name" -d "from_email=EMAIL" -d "to=to name" -d "to_email=EMAIL" -d "subject=sweet subject" -d "text=" -d "key=KEY"

the email requires a POST

if POST, you'll need the following data fields: 

from, from_email, to, to_email, subject, text, key (use the one in the code for now).

Email form(send only): http://thanh-email-service.herokuapp.com/email_service/form_test.php

PHP example of a curl:

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
            


