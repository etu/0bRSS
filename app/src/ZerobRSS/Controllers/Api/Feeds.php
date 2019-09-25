<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use ZerobRSS\Controllers\AbstractAuth;
use ZerobRSS\Dao\Feeds as FeedsDao;
use ZerobRSS\Dao\UserApiTokens as UserApiTokensDao;

class Feeds extends AbstractAuth
{
    /** @var FeedsDao */
    private $feedsDao;

    /** @var UserApiTokensDao */
    protected $userApiTokensDao;

    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(FeedsDao $feedsDao, UserApiTokensDao $userApiTokensDao, StreamFactory $streamFactory)
    {
        $this->feedsDao = $feedsDao;
        $this->streamFactory = $streamFactory;
        $this->userApiTokensDao = $userApiTokensDao;
    }

    public function get(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);
        $feedId = isset($args['id']) ? ((int) $args['id']) : null;

        // Fetch feeds
        $result = $this->feedsDao->getFeeds($userId, $feedId)->fetchAll();

        if (null !== $feedId) {
            $result = $result[0];
        }

        // Set response header
        $response = $response->withHeader('Content-Type', 'application/javascript');

        // Return data
        return $response->withBody($this->streamFactory->createStream(json_encode($result)));
    }

    public function post(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);
        $requestData = (object) $request->getParsedBody();

        // Create feed
        $feedId = $this->feedsDao->create($userId, [
            'name' => $requestData->name,
            'website_uri' => $requestData->website_uri,
            'feed_uri' => $requestData->feed_uri,
            'update_interval' => $requestData->update_interval
        ]);

        // Redirect to newly created resource
        return $response->withHeader('Location', '/api/v1/feeds/'.$feedId.'?token='.$request->getQueryParams()['token'])
            ->withStatus(302);
    }

    public function put(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);
        $requestData = (object) $request->getParsedBody();
        $feedId = isset($args['id']) ? ((int) $args['id']) : null;

        // Fetch feed
        $feed = $this->feedsDao->getFeeds($userId, $feedId)->fetch();

        if (false !== $feed) {
            try {
                $this->feedsDao->update((int) $feed->id, [
                    'name' => $requestData->name,
                    'website_uri' => $requestData->website_uri,
                    'feed_uri' => $requestData->feed_uri,
                    'update_interval' => $requestData->update_interval
                ]);
            } catch (\Exception $e) {
                return $response->withStatus(400);
            }

            return $response->withStatus(200);
        }

        return $response->withStatus(403);
    }

    public function delete(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);
        $feedId = isset($args['id']) ? ((int) $args['id']) : null;

        // Fetch feed
        $feed = $this->feedsDao->getFeeds($userId, $feedId)->fetch();

        if (false !== $feed) {
            try {
                $this->feedsDao->delete((int) $feed->id);
            } catch (\Exception $e) {
                return $response->withStatus(400);
            }

            return $response->withStatus(200);
        }

        return $response->withStatus(403);
    }
}
