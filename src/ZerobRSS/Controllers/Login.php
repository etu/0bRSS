<?php
namespace ZerobRSS\Controllers;

use \ZerobRSS\Dao\Users as UsersDao;

use \Slim\Slim;
use \JeremyKendall\Password\PasswordValidator;
use \JeremyKendall\Password\Result;

class Login
{
    /** @var Slim */
    private $slim;

    /** @var UsersDao */
    private $usersDao;

    /** @var PasswordValidator */
    private $passwordValidator;

    public function __construct(Slim $slim, UsersDao $usersDao, PasswordValidator $passwordValidator)
    {
        $this->slim = $slim;
        $this->usersDao = $usersDao;
        $this->passwordValidator = $passwordValidator;
    }

    public function get($msg = null)
    {
        $data = [];

        if (!is_null($msg)) {
            $data['message'] = $msg;
        }

        $this->slim->render(
            'login.twig',
            $data
        );
    }

    public function post()
    {
        $email    = $this->slim->request->post('email');
        $password = $this->slim->request->post('password');



        // Use this to hash a password, cost by default is 10 though, this should be in config
        // $this->passwordValidator->setOptions(['cost' => 9]);
        // $hash = $this->passwordValidator->rehash($password);



        // Fetch the User row from the database
        if ($user = $this->usersDao->getUser($email, 'email')->fetch()) {
            $result = $this->passwordValidator->isValid($password, $user->password);



            if ($result->getCode() === Result::SUCCESS_PASSWORD_REHASHED) {
                // Needs rehash, should update database with contents of: $result->getPassword();
            }



            if ($result->isValid()) {
                // Password is valid
                // $this->slim->response->headers->set('Location', $this->slim->request->getRootUri().'/');
                echo 'password is valid, go home';
                return;
            }
        }


        // If not caught in the if-statement above, no user exists and then we draw the loginpage with a message
        $this->get('No user with this Email/Password combination is registred.');
    }
}
