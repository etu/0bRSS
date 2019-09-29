'use strict';

class Feeds {
    constructor() {
        this.loadFeeds();
    }

    /**
     * Load feeds from API and trigger the drawing function
     */
    async loadFeeds() {
        // Build API URI
        var uri = window.ZerobRSS.apiUri + '/v1/feeds?token=' + window.ZerobRSS.Auth.token;

        // Do request
        var response = await fetch(uri, {
            headers: {
                'Accept': 'application/json',
            },
        });

        // Parse response
        this.feeds = await response.json();

        this.drawAside();
    }

    /**
     * Draw feeds in the aside-bar
     */
    drawAside() {
        var template = `
          <nav>
            ${this.feeds.map(feed => `<a data-feed-id="${feed.id}">&#x1f4f0; ${feed.name} <span class="unread">${feed.unread}</span></a>`)}
          </nav>
        `;

        document.getElementById('aside-menu').innerHTML = template;
    }
}

window.ZerobRSS.Feeds = new Feeds();
