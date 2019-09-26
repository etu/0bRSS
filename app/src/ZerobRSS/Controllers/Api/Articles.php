<?php
declare(strict_types=1);

namespace ZerobRSS\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use ZerobRSS\Controllers\AbstractAuth;
use ZerobRSS\Dao\Articles as ArticlesDao;
use ZerobRSS\Dao\Feeds as FeedsDao;
use ZerobRSS\Dao\UserApiTokens as UserApiTokensDao;

class Articles extends AbstractAuth
{
    /** @var ArticlesDao */
    private $articlesDao;

    /** @var FeedsDao */
    private $feedsDao;

    /** @var UserApiTokensDao */
    protected $userApiTokensDao;

    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(
        ArticlesDao $articlesDao,
        FeedsDao $feedsDao,
        UserApiTokensDao $userApiTokensDao,
        StreamFactory $streamFactory
    ) {
        $this->articlesDao = $articlesDao;
        $this->feedsDao = $feedsDao;
        $this->streamFactory = $streamFactory;
        $this->userApiTokensDao = $userApiTokensDao;
    }

    /**
     * Get all articles from feed by feedId, use ?previousId to get articles older
     * than the choosen article ID.
     */
    public function get(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);

        $previousId = isset($request->getQueryParams()['previousId']) ?
            ((int) $request->getQueryParams()['previousId']) : null;
        $read = isset($request->getQueryParams()['read']) ? ((bool) $request->getQueryParams()['read']) : null;
        $feedId = isset($args['id']) ? ((int) $args['id']) : null;

        // Fetch feed
        $feed = $this->feedsDao->getFeeds($userId, $feedId)->fetch();

        if (false !== $feed) {
            // Fetch articles
            $articles = $this->articlesDao->getPagedArticles($feedId, $previousId, $read)->fetchAll();

            // Return data
            return $response->withHeader('Content-Type', 'application/javascript')
                ->withBody($this->streamFactory->createStream(json_encode($articles)));
        }

        return $response->withStatus(403);
    }

    /**
     * Get a single specific article
     */
    public function getArticle(Request $request, Response $response, array $args = []) : Response
    {
        // Prepare variables
        $userId = $this->authRequest($request);
        $articleIdentifier = $args['aid'] ?? '';

        $article = $this->articlesDao->getArticleByIdentifier($userId, $articleIdentifier)->fetch();

        if (false !== $article) {
            // Return data
            return $response->withHeader('Content-Type', 'application/javascript')
                ->withBody($this->streamFactory->createStream(json_encode($article)));
        }

        return $response->withStatus(404);
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
