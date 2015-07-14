<?php
namespace ZerobRSS\Controllers\Api;

use \Slim\Slim;
use \ZerobRSS\Dao\Articles as ArticlesDao;
use \ZerobRSS\Dao\Feeds as FeedsDao;

class Articles
{
    /** @var Slim */
    private $slim;

    /** @var ArticlesDao */
    private $articlesDao;

    /** @var FeedsDao */
    private $feedsDao;

    public function __construct(Slim $slim, ArticlesDao $articlesDao, FeedsDao $feedsDao)
    {
        $this->slim = $slim;
        $this->articlesDao = $articlesDao;
        $this->feedsDao = $feedsDao;

        $this->slim->response->headers->set('Content-Type', 'application/json');
    }

    /**
     * Get all articles from feed by feedId, use ?page to choose page to load
     * page is 0 indexed
     */
    public function get($feedId)
    {
        $page = $this->slim->request->get('page', 0);

        $feed = $this->feedsDao->getFeeds($_SESSION['user']['id'], $feedId)->fetch();

        if (false !== $feed) {
            $articles = $this->articlesDao->getPagedArticles($feedId, $page)->fetchAll();

            echo json_encode($articles);
            exit;
        }

        $this->slim->response->setStatus(403);
    }

    /**
     * Get a single specific article
     */
    public function getArticle($articleIdentifier)
    {
        $article = $this->articlesDao->getArticleByIdentifier($_SESSION['user']['id'], $articleIdentifier)->fetch();

        if (false !== $article) {
            echo json_encode($article);
            exit;
        }

        $this->slim->response->setStatus(403);
    }
}
