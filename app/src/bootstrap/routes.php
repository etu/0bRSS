<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use ZerobRSS\Controllers\Api\Articles;
use ZerobRSS\Controllers\Api\Feeds;
use ZerobRSS\Controllers\Index;
use ZerobRSS\Controllers\Js;
use ZerobRSS\Controllers\Login;
use ZerobRSS\Controllers\Logout;
use ZerobRSS\Controllers\Scss;

return function (App $app) {
    $app->get('/', Index::class);                                  # Needs: Auth
    $app->get('/feed/{id}', Index::class);                         # Needs: Auth
    $app->get('/assets/css/{file}', Scss::class);                  # Status: Done
    $app->get('/assets/js/{file}', Js::class);                     # Status: Done
    $app->get('/login', Login::class);                             # Status: Done
    $app->get('/logout', Logout::class);                           # Needs: Auth

    /** Route: /api/v1 */
    $app->group('/api/v1', function (Group $group) {
        /** Route: /api/v1/feeds */
        $group->group('/feeds', function (Group $group) {
            $group->get('', Feeds::class.':get');                  # Needs: Auth
            $group->get('/{id}', Feeds::class.':get');             # Needs: Auth
            $group->post('', Feeds::class.':post');                # Needs: Auth
            $group->put('/{id}', Feeds::class.':put');             # Needs: Auth
            $group->delete('/{id}', Feeds::class.':delete');       # Needs: Auth
            $group->get('/{id}/articles', Articles::class.':get'); # Needs: Auth
        });

        /** Route: /api/v1/articles */
        $group->group('/articles', function (Group $group) {
            $group->get('/{aid}', Articles::class.':getArticle');  # Needs: Auth
            $group->put('/{aid}', Articles::class.':put');         # Needs: Auth
        });
    });
};
