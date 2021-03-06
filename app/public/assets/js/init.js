'use strict';

/**
 * Setup Feeds object and draw aside
 */
window.ZerobRSS.Feeds = new Feeds();


/**
 * Set up Scroll object
 */
window.ZerobRSS.Scroll = new Scroll();
$('content').addEvent('scroll', (function () { window.ZerobRSS.Scroll.event(); }));


/**
 * Set up Keyboard navigation object
 */
window.ZerobRSS.Keyboard = new Keyboard();


/**
 * Set up routing
 */
window.ZerobRSS.Router = new Router().add(/feed\/(\d+)/, (function () {
    $('content').set('html', '');
    window.ZerobRSS.ArticleLoader = new ArticleLoader(arguments[0]);
    window.ZerobRSS.Keyboard.currentArticle = null;
})).route();

/**
 * Register Route events
 */
window.addEvent('hashchange', (function () { window.ZerobRSS.Router.route(); }));
window.addEvent('popstate',   (function () { window.ZerobRSS.Router.route(); }));
