# API with Lumen PHP Framework And JWT Auth


## Project Setup

This project requires latest [composer](https://getcomposer.org/) version and [git](https://git-scm.com/) to run.

```sh
$ git clone https://kisorniru@bitbucket.org/kisorniru/siddique-api.local.git
$ cd siddique-api.local
$ composer update
```

* copy and paste ``` .env.dist ``` file where it is AND rename it as ``` .env ``` file
* open ```.env``` file and change inside DATABASE_URL
    - DataBase_userName
    - DataBase_password
    - DataBase_host
    - DataBase_name

    - MAIL_DRIVER=smtp
	- MAIL_HOST=smtp.gmail.com
	- MAIL_PORT=587
	- MAIL_USERNAME=your@gmail.com
	- MAIL_PASSWORD=yourgmailpass
	- MAIL_ENCRYPTION=tls

## Project Installation

This project requires latest [composer](https://getcomposer.org/) version and [git](https://git-scm.com/) to run.

* run this command
```sh
$ composer create-project --prefer-dist laravel/lumen siddique-api.local
$ cd siddique-api.local
$ composer require tymon/jwt-auth:"^1.0@dev"
```

* inside `bootstrap/app.php`

// Uncomment this line
```sh
$app->withFacades();
$app->withEloquent();
$app->register(App\Providers\AuthServiceProvider::class);
```

// Add this line
```sh
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
```

// Uncomment this line
```sh
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);
```

* run this command for JWT Secret
```sh
$ php artisan jwt:secret
```
* For Email (Gmail SMTP) Issue

// Add this line into require section inside `composer.json` file
```sh
"illuminate/mail": "^5.7"
```

// run this command for composer update
```sh
$ composer update
```

// create `config` folder into root directory

// create `mail.php` file into config folder

// add bellow php code into `mail.php` file

```sh
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Laravel supports both SMTP and PHP's "mail" function as drivers for the
    | sending of e-mail. You may specify which one you're using throughout
    | your application here. By default, Laravel is setup for SMTP mail.
    |
    | Supported: "smtp", "mail", "sendmail", "mailgun", "mandrill", "ses", "log"
    |
    */

    'driver' => env('MAIL_DRIVER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Address
    |--------------------------------------------------------------------------
    |
    | Here you may provide the host address of the SMTP server used by your
    | applications. A default option is provided that is compatible with
    | the Mailgun mail service which will provide reliable deliveries.
    |
    */

    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Port
    |--------------------------------------------------------------------------
    |
    | This is the SMTP port used by your application to deliver e-mails to
    | users of the application. Like the host we have set this value to
    | stay compatible with the Mailgun e-mail application by default.
    |
    */

    'port' => env('MAIL_PORT', 587),

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => ['address' => null, 'name' => null],

    /*
    |--------------------------------------------------------------------------
    | E-Mail Encryption Protocol
    |--------------------------------------------------------------------------
    |
    | Here you may specify the encryption protocol that should be used when
    | the application send e-mail messages. A sensible default using the
    | transport layer security protocol should provide great security.
    |
    */

    'encryption' => env('MAIL_ENCRYPTION', 'tls'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Username
    |--------------------------------------------------------------------------
    |
    | If your SMTP server requires a username for authentication, you should
    | set it here. This will get used to authenticate with your server on
    | connection. You may also set the "password" value below this one.
    |
    */

    'username' => env('MAIL_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Password
    |--------------------------------------------------------------------------
    |
    | Here you may set the password required by your SMTP server to send out
    | messages from your application. This will be given to the server on
    | connection so that the application will be able to send messages.
    |
    */

    'password' => env('MAIL_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sendmail System Path
    |--------------------------------------------------------------------------
    |
    | When using the "sendmail" driver to send e-mails, we will need to know
    | the path to where Sendmail lives on this server. A default path has
    | been provided here, which will work well on most of your systems.
    |
    */

    'sendmail' => '/usr/sbin/sendmail -bs',

    /*
    |--------------------------------------------------------------------------
    | Mail "Pretend"
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, e-mail will not actually be sent over the
    | web and will instead be written to your application's logs files so
    | you may inspect the message. This is great for local development.
    |
    */

    'pretend' => env('MAIL_PRETEND', false),

];
```
// Add this line into `bootstrap/app.php` file

```sh
$app->register(\Illuminate\Mail\MailServiceProvider::class);
$app->configure('mail');
```
// Add this line into `.env` file

	- MAIL_DRIVER=smtp
	- MAIL_HOST=smtp.gmail.com
	- MAIL_PORT=587
	- MAIL_USERNAME=your@gmail.com
	- MAIL_PASSWORD=yourgmailpass
	- MAIL_ENCRYPTION=tls

* Sending Email `MailController.php`

```sh
<?php

....

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        ....
        $this->sendEmailNotification($email, $name, $subject, $for_view_data_2);
        ....
    }

    ....
    ....

    private function sendEmailNotification($to_email, $to_name, $to_subject, $for_view_data_2)
    {
        $from_email = env('MAIL_USERNAME');
        $from_name = "Email Test";
        $to_email = $to_email;
        $to_name = $to_name;
        $to_subject = $to_subject;

        $sentMail = Mail::send('view_file_name', ['for_view_data_1' => $to_name, 'for_view_data_2' => $for_view_data_2 ], function($mail) use ($from_email, $from_name, $to_email, $to_name, $to_subject)
            {
                $mail->from($from_email, $from_name);
                $mail->to($to_email, $to_name);
                $mail->subject($to_subject);
            }
        );
    }
}
```

## Any Problem

For any problem to run this project, please contact with me 

```sh 
email : ajayit2020@gmail.com
phone : coming soon. 
```
php artisan migrage too



server {
        listen 80;
        listen [::]:80;  

        root /var/www/examclass.in/html/demo/exam;

        index index.php index.html index.htm index.nginx-debian.html;

        server_name examclass.in www.examclass.in;

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.2-fpm.sock;
        }

        location ~ /\.ht {
                deny all;
        }

        location ~ /.well-known {
                allow all;
        }
}

