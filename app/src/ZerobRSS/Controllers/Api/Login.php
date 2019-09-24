<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers\Api;

use JeremyKendall\Password\PasswordValidator;
use JeremyKendall\Password\Result;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\StreamFactory;
use ZerobRSS\Config;
use ZerobRSS\Dao\UserApiTokens as UserApiTokensDao;
use ZerobRSS\Dao\Users as UsersDao;

class Login
{
    /** @var Config */
    private $config;

    /** @var LoggerInterface */
    private $logger;

    /** @var PasswordValidator */
    private $passwordValidator;

    /** @var StreamFactory */
    private $streamFactory;

    /** @var UserApiTokensDao */
    private $userApiTokensDao;

    /** @var UsersDao */
    private $usersDao;

    public function __construct(
        Config $config,
        LoggerInterface $logger,
        PasswordValidator $passwordValidator,
        StreamFactory $streamFactory,
        UserApiTokensDao $userApiTokensDao,
        UsersDao $usersDao
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->passwordValidator = $passwordValidator;
        $this->streamFactory = $streamFactory;
        $this->userApiTokensDao = $userApiTokensDao;
        $this->usersDao = $usersDao;
    }

    public function __invoke(Request $request, Response $response, array $args = []) : Response
    {
        $this->passwordValidator->setOptions(['cost' => $this->config->bcryptPasswordCost]);

        $form = $request->getParsedBody();

        if (isset($form['email'], $form['password'])) {
            $user = $this->usersDao->getUser($form['email'], 'email')->fetch();

            $result = $this->passwordValidator->isValid($form['password'], $user->password);

            // Update password in database to reflect the new updated hash
            if ($result->getCode() === Result::SUCCESS_PASSWORD_REHASHED) {
                $this->usersDao->update((int) $user->id, [
                    'password' => $result->getPassword(),
                    'updated' => date('Y-m-d H:i:s'),
                ]);
            }

            // Password is valid
            if ($result->isValid()) {
                // Update last login
                $this->usersDao->update((int) $user->id, [
                    'last_login' => date('Y-m-d H:i:s'),
                ]);

                // Create api token
                $token = $this->userApiTokensDao->createTokenForUser((int) $user->id);

                // Set response header
                $response = $response->withHeader('Content-Type', 'application/javascript');

                // Return data
                return $response->withBody($this->streamFactory->createStream(json_encode([
                    'token' => $token,
                ])));
            }
        }

        throw new \Exception('No email/password found for given combination');
    }
}
