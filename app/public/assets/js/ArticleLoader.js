'use strict';

class ArticleLoader {
    constructor() {
    }

    async loadArticles(feedId, unread) {
        // Build API URI
        var uri = window.ZerobRSS.apiUri + '/v1/feeds/' + feedId + '/articles?token=' + window.ZerobRSS.Auth.token;

        if (unread > 0) {
            uri += '&read=false';
        }

        // Do request
        var response = await fetch(uri, {
            headers: {
                'Accept': 'application/json',
            },
        });

        // Parse response
        var articles = await response.json();

        var template = `
            ${articles.map(article => `
              <article>
                  <header>
                      ${article.title}
                      <span class="extras">
                          <a href="${article.uri}" target="_blank">&#x1f517;</a>
                          <i>&#x2605; &#x2606;</i>
                      </span>
                  </header>
                  <div class="article-body">
                      ${article.body}
                  </div>
                  <footer>
                     ${article.date}
                  </footer>
              </article>
            `)}
        `;

        document.getElementById('content').innerHTML += template;
    }
}

window.ZerobRSS.ArticleLoader = new ArticleLoader();
