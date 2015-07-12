<?php
namespace ZerobRSS\Controllers;

use \Slim\Slim;

use \ZerobRSS\Dao\Feeds as FeedsDao;
use \ZerobRSS\Dao\Articles as ArticlesDao;

class Read
{
    /** @var Slim */
    private $slim;

    /** @var FeedsDao */
    private $feedsDao;

    /** @var ArticlesDao */
    private $articlesDao;

    public function __construct(Slim $slim, FeedsDao $feedsDao, ArticlesDao $articlesDao)
    {
        $this->slim = $slim;
        $this->feedsDao = $feedsDao;
        $this->articlesDao = $articlesDao;
    }

    public function get($feedId)
    {
        $this->slim->render(
            'index.twig',
            [
                'feeds' => $this->feedsDao->getFeeds($_SESSION['user']['id'])->fetchAll(),
                'articles' => $this->articlesDao->getArticles($feedId, $_SESSION['user']['id'])->fetchAll()
            ]
        );
    }
}
