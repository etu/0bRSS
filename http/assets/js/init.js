'use strict';

/**
 * Setup Feeds object and draw aside
 */
window.ZerobRSS.Feeds = new Feeds();


/**
 * Set up routing
 */
window.ZerobRSS.Router = new Router();

/**
 * Register Route events
 */
window.addEvent('hashchange', (function () { window.ZerobRSS.Router.match(); }));
window.addEvent('popstate',   (function () { window.ZerobRSS.Router.match(); }));

/**
 * Register routes
 */
window.ZerobRSS.Router.add(/feed\/(\d+)/, (function () {
    $('content').set('html', '');
    window.ZerobRSS.ArticleLoader = new ArticleLoader(arguments[0]);
}));

/**
 * Trigger routing
 */
window.ZerobRSS.Router.match();
