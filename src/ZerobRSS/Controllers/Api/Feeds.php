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

    public function get()
    {
        echo json_encode($this->feedsDao->getFeeds($_SESSION['user']['id'])->fetchAll());
    }
}
