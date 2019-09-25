<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use ZerobRSS\Dao\UserApiTokens as UserApiTokensDao;

abstract class AbstractAuth
{
    /** @var UserApiTokensDao */
    protected $userApiTokensDao;

    protected function authRequest(Request $request) : int
    {
        $token = $request->getQueryParams()['token'] ?? null;

        if ($token === null) {
            throw new \Exception('Missing parameter token');
        }

        $userId = $this->userApiTokensDao->validateUserToken($token);

        if ($userId === null) {
            throw new \Exception('Invalid API Token');
        }

        return $userId;
    }
}
