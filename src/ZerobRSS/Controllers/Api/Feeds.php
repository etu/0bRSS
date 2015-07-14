<?php
namespace ZerobRSS\Controllers\Api;

use \Slim\Slim;
use \ZerobRSS\Dao\Feeds as FeedsDao;

class Feeds
{
    /** @var Slim */
    private $slim;

    /** @var FeedsDao */
    private $feedsDao;

    public function __construct(Slim $slim, FeedsDao $feedsDao)
    {
        $this->slim = $slim;
        $this->feedsDao = $feedsDao;

        $this->slim->response->headers->set('Content-Type', 'application/json');
    }

    public function get($feedId = null)
    {
        $result = $this->feedsDao->getFeeds($_SESSION['user']['id'], $feedId)->fetchAll();

        if (null !== $feedId) {
            $result = $result[0];
        }

        echo json_encode($result);
    }

    public function post()
    {
        // Read JSON from Body-input
        $requestData = json_decode($this->slim->request->getBody());

        // Create feed
        $feedId = $this->feedsDao->create($_SESSION['user']['id'], $requestData);

        // Redirect to the new API-Resource to tell the client where it is
        $this->slim->redirect($this->slim->request->getRootUri().'/api/feeds/'.$feedId);
    }
}
