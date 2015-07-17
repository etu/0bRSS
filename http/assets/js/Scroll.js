'use strict';

var Scroll = new Class({
    event: function () {
        var elem = $('content');

        var distanceToBottom = elem.getScrollSize().y - (elem.scrollTop + elem.getSize().y);

        if (distanceToBottom < 500) {
            window.ZerobRSS.ArticleLoader.page++;
            window.ZerobRSS.ArticleLoader.getArticles();
        }
    }
});
