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
use ZerobRSS\Controllers\Api\Login as ApiLogin;
use ZerobRSS\Controllers\Api\Logout as ApiLogout;
use ZerobRSS\Controllers\Index;
use ZerobRSS\Controllers\Js;
use ZerobRSS\Controllers\Login;
use ZerobRSS\Controllers\Scss;

return function (App $app) {
    $app->get('/', Index::class);                                  # Needs: Auth
    $app->get('/assets/css/{file}', Scss::class);                  # Status: Done
    $app->get('/assets/js/{file}', Js::class);                     # Status: Done
    $app->get('/feed/{id}', Index::class);                         # Needs: Auth
    $app->get('/login', Login::class);                             # Status: Done

    /** Route: /api/v1 */
    $app->group('/api/v1', function (Group $group) {
        $group->post('/login', ApiLogin::class);                   # Status: Done
        $group->get('/logout', ApiLogout::class);                  # Status: Done

        /** Route: /api/v1/feeds */
        $group->group('/feeds', function (Group $group) {
            $group->get('', Feeds::class.':get');                  # Status: Done
            $group->get('/{id}', Feeds::class.':get');             # Status: Done
            $group->post('', Feeds::class.':post');                # Status: Done
            $group->put('/{id}', Feeds::class.':put');             # Status: Done
            $group->delete('/{id}', Feeds::class.':delete');       # Status: Done
            $group->get('/{id}/articles', Articles::class.':get'); # Status: Done
        });

        /** Route: /api/v1/articles */
        $group->group('/articles', function (Group $group) {
            $group->get('/{aid}', Articles::class.':getArticle');  # Status: Done
            $group->put('/{aid}', Articles::class.':put');         # Status: Done
        });
    });
};
