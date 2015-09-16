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
     * Get all articles from feed by feedId, use ?previousId to get articles older
     * than the choosen article ID.
     */
    public function get($feedId)
    {
        $previousId = $this->slim->request->get('previousId', null);
        $read = $this->slim->request->get('read', null);

        $feed = $this->feedsDao->getFeeds($_SESSION['user']['id'], $feedId)->fetch();

        if (false !== $feed) {
            $articles = $this->articlesDao->getPagedArticles($feedId, $previousId, $read)->fetchAll();

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

    /**
     * Update read/starred of specific article
     */
    public function put($articleIdentifier)
    {
        // Read JSON from Body-input
        $requestData = json_decode($this->slim->request->getBody());

        $article = $this->articlesDao->getArticleByIdentifier($_SESSION['user']['id'], $articleIdentifier)->fetch();

        if (false !== $article) {
            $values = [
                'identifier' => $article->identifier,
                'feed_id' => $article->feed_id
            ];

            if (isset($requestData->read)) {
                $values['read'] = ($requestData->read) ? 'true' : 'false';
            }

            if (isset($requestData->starred)) {
                $values['starred'] = ($requestData->starred) ? 'true' : 'false';
            }

            $this->articlesDao->update($article->identifier, $values);
            exit;
        }

        $this->slim->response->setStatus(403);
    }
}
