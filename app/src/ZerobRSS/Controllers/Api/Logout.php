<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ZerobRSS\Controllers\AbstractAuth;
use ZerobRSS\Dao\UserApiTokens as UserApiTokensDao;

class Logout extends AbstractAuth
{
    /** @var UserApiTokensDao */
    protected $userApiTokensDao;

    public function __construct(UserApiTokensDao $userApiTokensDao)
    {
        $this->userApiTokensDao = $userApiTokensDao;
    }

    public function __invoke(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);
        $token = $request->getQueryParams()['token'] ?? null;

        if ($userId && $token) {
            if ($this->userApiTokensDao->deleteToken($token) === 1) {
                return $response->withStatus(200);
            }
        }

        return $response->withStatus(401);
    }
}
