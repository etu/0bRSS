'use strict';

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
            url: window.ZerobRSS.apiUri + '/v1/feeds/' + this.feed + '/articles?page=' + this.page,
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
