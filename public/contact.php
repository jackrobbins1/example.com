<?php

require '../core/About/src/Validation/Validate.php';

$valid = new About\Validation\Validate();

$args = [
  'name'=>FILTER_SANITIZE_STRING,
  'email'=>FILTER_SANITIZE_EMAIL, 
  'message'=>FILTER_SANITIZE_STRING,
  'subject'=>FILTER_SANITIZE_STRING,
];

$input = filter_input_array(INPUT_POST, $args);

if(!empty($input)){

  $valid->validation = [
    'email'=>[[
      'rule'=>'email',
      'message'=>'Please enter a valid email'
    ],[
      'rule'=>'notEmpty',
      'message'=>'Please enter an email'
    ]],
    'name'=>[[
      'rule'=>'notEmpty',
      'message'=>'Please enter your name'
    ]],
    'message'=>[[
      'rule'=>'notEmpty',
      'message'=>'Please enter a message'
    ]]

  ];

  $valid->check($input);

  if(empty($valid->errors)){

    require '../vendor/autoload.php';
    require '../../config.php';

    //use Mailgun\Mailgun;

    $mgClient = new Mailgun\Mailgun('key-5f89cdfe4c5b44706264ade1f4226e23');
    $domain = MG_DOMAIN;

    $result = $mgClient->sendMessage(
      $domain,
      [
        'from'=>"Mailgun Sandbox <postmaster@{$domain}>",
        'to'=>'Jackson Robbins <jackrobbins1@gmail.com>',
        'subject'=>$input['subject'],
        'html'=>"<b>Name</b>: {$input['name']}<br><br>" .
          "<b>Email</b>: {$input['email']}<br><br>" .
          "<b>Message</b><br>{$input['message']}"
      ]
      );

  header('LOCATION: thanks.html');
}else{
  $message = "<div class =\"message-error\">The form has errors!</div>";
}
}

$message = (!empty($message)?$message:null); 

$pageTitle = "Contact";
$description = "Send Jackson Robbins a message.";
$content = <<<EOT
  <main>
          <h1>Contact Jackson</h1>
          {$message}
      
    <form action="contact.php" method="POST">
      
      <input type="hidden" name="_subject" value="You Got Mail!">
      
      <div>
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{$valid->userInput('name')}">
        <div class='message-error'>
          {$valid->error('name')}
        </div>
      </div>

      <div>
        <label for="email">Email</label>
        <input id="email" type="text" name="email" value="{$valid->userInput('email')}">  
      
        <div class='message-error'>
          {$valid->error('email')}
        </div>
      </div>

      <div>
        <label for="message">Message</label>
        
        <textarea id="message" name="message">
          {$valid->userInput('email')}
        </textarea>
      
        <div class='message-error'>
          {$valid->error('message')}
        </div>
      </div>

      <div>
        <input class="btn" type="submit" value="Send">
      </div>

    </form>
  </main>
EOT;

require '../core/layout.php';