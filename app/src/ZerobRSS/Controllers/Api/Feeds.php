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

    public function post()
    {
        // Read JSON from Body-input
        $requestData = json_decode($this->slim->request->getBody());

        // Create feed
        $feedId = $this->feedsDao->create($_SESSION['user']['id'], [
            'name' => $requestData->name,
            'website_uri' => $requestData->website_uri,
            'feed_uri' => $requestData->feed_uri,
            'update_interval' => $requestData->update_interval
        ]);

        // Redirect to the new API-Resource to tell the client where it is
        $this->slim->redirect($this->slim->request->getRootUri().'/api/feeds/'.$feedId);
    }

    public function put($feedId)
    {
        // Read JSON from Body-input
        $requestData = json_decode($this->slim->request->getBody());

        $feed = $this->feedsDao->getFeeds($_SESSION['user']['id'], $feedId)->fetch();

        if (false !== $feed) {
            try {
                $this->feedsDao->update($feed->id, [
                    'name' => $requestData->name,
                    'website_uri' => $requestData->website_uri,
                    'feed_uri' => $requestData->feed_uri,
                    'update_interval' => $requestData->update_interval
                ]);

            } catch (\Exception $e) {
                $this->slim->response->setStatus(400);
            }

            return;
        }

        $this->slim->response->setStatus(403);
    }

    public function delete($feedId)
    {
        $feed = $this->feedsDao->getFeeds($_SESSION['user']['id'], $feedId)->fetch();

        if (false !== $feed) {
            try {
                $this->feedsDao->delete($feed->id);
            } catch (\Exception $e) {
            }

            return;
        }

        $this->slim->response->setStatus(403);
    }
}
