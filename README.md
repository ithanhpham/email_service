## README.md Outline


* Email service

project: An Email service that uses two proiders; a main one and a backup in case that fails

track: backend

reason: I chose the email service because I enjoy writing backend code, have some experience using mailgun in our dev portal and it's something that can be re-used at some other point. I used PHP as the backend because I am most familiar with that language. I have 2.5+ years with it. My QA background always has me thinking about use cases which helps with the quality.

trade-offs/more: The usual trade-offs of speed vs quality vs inexpensive comes into play. Due to time constraints, I wasn't able to incorporate this into a framework like CodeIgniter to give it a MVC framework. This would allow routing to be seemless and less time on writing sanitization code.

Like with any API that we write at my current company, I would like to write unit tests. This would especially be important for all the use cases due to dependancies on the third parties. Getting this deployed on Amazon was also a challenge. Again, this is due to time constraints. With more time, I'd like to have a nice interface test out the service.

NOTE: I've been trying to get my domain whitelisting from mailgun and while I updated one of the DNS records, I wasn't able to add the DKIM or unless the TTL is longer than 48 hrs. As a result, I noticed that yahoo accounts had difficulty receiving the emails while the gmail accounts didn't have an issue.

I don't have much public backend code I can share, but I encourage you to register with the thismoment developer platform and you'll be able to see our docs and apis which I helped write.

USAGE:

* the email requires a POST

* it needs to call http://thanhsguitar.com/projects/email_service/controller/mailer
test using Google's rest console and hitting the API using the required fields below (http://screencast.com/t/FfTwURKZ, http://screencast.com/t/YodfweUQ9wI)

* you should get a response but there is a bug in the REST console that you need to attach an image as port of the request

$email_data  = array('from'       => 'Thanh Pham',
                     'from_email' => 'useagmailaccount @gmail.com',
                     'to'         => 'Tester',
                     'to_email'   => 'asdf+1@gmail.com',
                     'subject'    => time() . ' Just seeing...',
                     'text'       => 'if some email magic is about to happen!');

* the logic in the controller will now try to clean the fields and make a cURL call to mail gun. If it succeeds, it will kick back a 200 status code but if not, it will try to send it via mandrill.

* http://thanhsguitar.com/projects/email_service/form_test.php can test 

* or the backend call that hardcodes items post_test.php


Email service

project

The purpose of this service is to send out email with a backup. One of the strengths of this service is that if a dependency on 3rd party service fails, the service will try to use a different email api. The main email service is MailGun and the backup is Mandrill.

application track

backend developer

reason why

I chose the email service because I enjoy writing backend code, have some experience using mailgun in my current job's dev portal and it's something that can be re-used at some other point. I used PHP as the backend because I am most familiar with that language. I have 2.5+ years with it. My QA background always has me thinking about use cases which helps with the quality of the application.

trade-offs/more info

The usual trade-offs of speed vs quality vs price comes into play. Due to time constraints, I wasn't able to incorporate this into a framework like CodeIgniter to give it a MVC framework. This would allow routing to be seemless and less time on writing sanitization code. Like with any API that we write at my current company, I would like to write more detailed unit tests. This would especially be important for all the use cases due to dependancies on the third parties. Getting this deployed on Amazon was also a challenge. Again, this is due to time constraints. With more time, I'd like to have a nice interface test out the service.
logic

the logic in the controller:
check if it is a POST request
check all params
try to use mail gun to send email
if fails, use Mandrill to send email
fails if both services fail
returns JSON
Example:

curl -X POST 'https://thanh-email-service.herokuapp.com/email_service/controller/mailer.php' -d "from=from name" -d "from_email=EMAIL" -d "to=to name" -d "to_email=EMAIL" -d "subject=sweet subject" -d "text=" -d "key=KEY"
the email requires a POST
if POST, you'll need the following data fields: from, from_email, to, to_email, subject, text, key (use the one in the code for now).
Email form(send only)
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
            


