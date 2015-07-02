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



        // Set password hashing cost for bcrypt, default is 10, but more is better
        $config = require(PROJECT_ROOT.'/config.php');
        $this->passwordValidator->setOptions(['cost' => $config['bcrypt-password-cost']]);



        // Fetch the User row from the database
        if ($user = $this->usersDao->getUser($email, 'email')->fetch()) {
            $result = $this->passwordValidator->isValid($password, $user->password);



            if ($result->getCode() === Result::SUCCESS_PASSWORD_REHASHED) {
                // Update password in database to reflect the new updated hash
                $this->usersDao->update($user->id, [
                    'password' => $result->getPassword(),
                    'updated' => date('Y-m-d H:i:s')
                ]);
            }



            if ($result->isValid()) {
                // Password is valid
                $this->usersDao->update($user->id, [
                    'last_login' => date('Y-m-d H:i:s')
                ]);

                // Set User-data to session
                $_SESSION['user']['id']     = $user->id;
                $_SESSION['user']['email']  = $user->email;
                $_SESSION['user']['name']   = $user->name;
                $_SESSION['user']['groups'] = [];

                // Loop user groups and add them to the session
                foreach ($this->usersDao->getGroups($user->id)->fetchAll() as $group) {
                    $_SESSION['user']['groups'][] = $group->name;
                }

                // Redirect to homepage
                $this->slim->response->headers->set('Location', $this->slim->request->getRootUri().'/');
            }
        }


        // If not caught in the if-statement above, no user exists and then we draw the loginpage with a message
        $this->get('No user with this Email/Password combination is registred.');
    }
}
