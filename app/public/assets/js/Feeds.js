'use strict';


/**
 * Class to fetch the feed list and draw the aside
 */
var Feeds = new Class({
    feeds: [],

    initialize: function () {
        this.loadFeeds();
    },

    /**
     * Load feeds from API and trigger the drawing function
     */
    loadFeeds: function () {
        new Request.JSON({
            method: 'get',
            url: window.ZerobRSS.apiUri + '/v1/feeds',
            onComplete: function (response) {
                window.ZerobRSS.Feeds.feeds = response;

                window.ZerobRSS.Feeds.drawAside();
            }
        }).send();
    },

    /**
     * Draw feeds in the aside-bar
     */
    drawAside: function () {
        var template = Handlebars.compile($('feed-menu-template').get('html'));

        this.feeds.each(function(feed) {
            var a = new Element('a');

            a.set('data-feed-id', feed.id);
            a.set('html', template(feed));

            a.addEvent('click', (function () {
                window.ZerobRSS.Router.nav('/feed/' + feed.id)
            }));

            a.inject($$('#aside-menu nav')[0]);
        });
    }
});
