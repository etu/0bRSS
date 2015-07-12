<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;

use \ZerobRSS\Dao\Feeds as FeedsDao;

class Index
{
    /** @var Slim */
    private $slim;

    /** @var FeedsDao */
    private $feedsDao;

    public function __construct(Slim $slim, FeedsDao $feedsDao)
    {
        $this->slim = $slim;
        $this->feedsDao = $feedsDao;
    }

    public function get()
    {
        $this->slim->log->info('Slim "/" route');

        $this->slim->render(
            'index.twig',
            [
                'feeds' => $this->feedsDao->getFeeds($_SESSION['user']['id'])->fetchAll()
            ]
        );
    }
}
