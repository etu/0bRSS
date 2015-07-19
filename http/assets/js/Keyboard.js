'use strict';

var Keyboard = new Class({
    currentArticle: null,

    initialize: function () {
        window.addEvent('keydown', function (e) {
            switch (e.key) {
                case 'n':
                    window.ZerobRSS.Keyboard.scrollToNext();
                    break;
                case 'p':
                    window.ZerobRSS.Keyboard.scrollToPrevious();
                    break;
                case 'v':
                    window.ZerobRSS.Keyboard.visitArticle();
                    break;
                case 'm':
                    window.ZerobRSS.Keyboard.toggleRead();
                    break;
            }
        });
    },



    /**
     * Scroll to next article
     */
    scrollToNext: function() {
        // Default to first article
        var nextArticle = $('content').getChildren()[0];

        // If we visit a article at the moment, find next
        if (null !== window.ZerobRSS.Keyboard.currentArticle) {
            nextArticle = $$('article[data-id=' + window.ZerobRSS.Keyboard.currentArticle + ']')[0].getNext();
        }

        if (nextArticle) {
            // Set currentArticle ID
            window.ZerobRSS.Keyboard.currentArticle = nextArticle.get('data-id');

            // Scroll to article
            nextArticle.scrollIntoView();

            // Mark as clicked
            window.ZerobRSS.ArticleLoader.clickArticle(nextArticle.get('data-id'));
        }

        // Trigger scroll event to get more articles if needed
        window.ZerobRSS.Scroll.event();
    },



    /**
     * Scroll to previous article
     */
    scrollToPrevious: function() {
        // Default to first article
        var nextArticle = $('content').getChildren()[0];

        // If we visit a article at the moment, find next
        if (null !== window.ZerobRSS.Keyboard.currentArticle) {
            nextArticle = $$('article[data-id=' + window.ZerobRSS.Keyboard.currentArticle + ']')[0].getPrevious();
        }

        if (nextArticle) {
            // Set currentArticle ID
            window.ZerobRSS.Keyboard.currentArticle = nextArticle.get('data-id');

            // Scroll to article
            nextArticle.scrollIntoView();

            // Mark as clicked
            window.ZerobRSS.ArticleLoader.clickArticle(nextArticle.get('data-id'));
        }

        // Trigger scroll event to get more articles if needed
        window.ZerobRSS.Scroll.event();
    },



    /**
     * Open article in new tab/window/whatever, just a new one
     */
    visitArticle: function() {
        var link = $$('article[data-id=' + window.ZerobRSS.Keyboard.currentArticle + '] > header > span.extras > a')[0];

        window.open(link.href, '_blank');
    },



    /**
     * Toggle read
     */
    toggleRead: function() {
        var article = $$('article[data-id=' + this.currentArticle + ']')[0];

        new Request.JSON({
            url: window.ZerobRSS.apiUri + '/v1/articles/' + this.currentArticle,
            data: JSON.encode({'read': !article.hasClass('read')}),
            emulation: false,
            onComplete: function (response) {
                var wasRead = article.hasClass('read');

                article.toggleClass('read');

                var read = $$('#aside-menu > nav > a[data-feed-id=' + article.get('data-feed-id') + '] .unread')[0];

                if (!wasRead) {
                    read.set('html', parseInt(read.get('html')) - 1);
                } else {
                    read.set('html', parseInt(read.get('html')) + 1);
                }
            }
        }).put();
    }
});
