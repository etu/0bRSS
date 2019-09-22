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
    $app->get('/', Index::class);                                  # Needs: DB, Auth
    $app->get('/feed/{id}', Index::class);                         # Needs: DB, Auth
    $app->get('/assets/css/{file}', Scss::class);                  # Needs: Nothing
    $app->get('/assets/js/{file}', Js::class);                     # Needs: Nothing

    $app->group('/login', function (Group $group) {
        $group->get('', Login::class.':get');                      # Needs: Nothing
        $group->post('', Login::class.':post');                    # Needs: DB
    });

    $app->get('/logout', Logout::class);                           # Needs: DB, Auth

    /** Route: /api/v1 */
    $app->group('/api/v1', function (Group $group) {
        /** Route: /api/v1/feeds */
        $group->group('/feeds', function (Group $group) {
            $group->get('', Feeds::class.':get');                  # Needs: DB, Auth
            $group->get('/{id}', Feeds::class.':get');             # Needs: DB, Auth
            $group->post('', Feeds::class.':post');                # Needs: DB, Auth
            $group->put('/{id}', Feeds::class.':put');             # Needs: DB, Auth
            $group->delete('/{id}', Feeds::class.':delete');       # Needs: DB, Auth
            $group->get('/{id}/articles', Articles::class.':get'); # Needs: DB, Auth
        });

        /** Route: /api/v1/articles */
        $group->group('/articles', function (Group $group) {
            $group->get('/{aid}', Articles::class.':getArticle');  # Needs: DB, Auth
            $group->put('/{aid}', Articles::class.':put');         # Needs: DB, Auth
        });
    });
};
