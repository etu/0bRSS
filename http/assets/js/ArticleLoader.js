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

                    a.set('data-id', article.identifier);
                    a.set('html', template(article));

                    if (article.read) {
                        a.addClass('read');
                    }

                    a.addEvent('click', (function (e) {
                        window.ZerobRSS.ArticleLoader.clickArticle(e.target.getParent('article').get('data-id'));
                    }));

                    a.inject($('content'));
                });
            }
        }).send();
    },

    clickArticle: function (id) {
        window.ZerobRSS.Keyboard.currentArticle = id;

        $$('#content > article').each(function (elem) {
            elem.removeClass('active');
        });
        $$('article[data-id=' + id + ']')[0].addClass('active');

        new Request.JSON({
            url: window.ZerobRSS.apiUri + '/v1/articles/' + id,
            data: JSON.encode({'read': true}),
            emulation: false,
            onComplete: function (response) {
                $$('article[data-id=' + id + ']')[0].addClass('read');
            }
        }).put();
    }
});
