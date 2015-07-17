'use strict';


/**
 * Class to fetch the feed list and draw the aside
 */
var Feeds = new Class({
    feeds: [],
    initialize: function () {
        this.loadFeeds();
    },

    /** Load feeds from API and trigger the drawing function */
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

    drawAside: function () {
        var template = Handlebars.compile($('feed-menu-template').get('html'));

        this.feeds.each(function(feed) {
            var a = new Element('a');

            a.set('html', template(feed));

            a.addEvent('click', (function () {
                window.ZerobRSS.Router.nav('/feed/' + feed.id)
            }));

            a.inject($$('#aside-menu nav')[0]);
        });
    }
});

var ArticleLoader = new Class({
    feed: 12,
    page: 0,

    initialize: function (feed) {
        this.feed = feed;

        this.getArticles();
    },

    getArticles: function () {
        new Request.JSON({
            method: 'get',
            url: window.ZerobRSS.apiUri + '/v1/feeds/' + this.feed + '/articles',
            onComplete: function (response) {
                var template = Handlebars.compile($('news-card-template').get('html'));

                response.each(function (article) {
                    var a = new Element('article');

                    a.set('html', template(article));

                    a.inject($('content'));
                });
            }
        }).send();
    }
});

window.ZerobRSS.Feeds = new Feeds();
