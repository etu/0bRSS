'use strict';

var Router = new Class({
    Implements: [ Options, Events ],
    options: {
        routes: [],
        mode: !!(history.pushState) ? 'history' : 'hash',
        root: '/',
        previousMatch: null
    },


    initialize: function (options) {
        this.setOptions(options);
    },


    /**
     * Get the current URI fragment
     */
    getFragment: function () {
        var fragment = '';

        // Hash fragment matching
        fragment = window.location.hash.substr(2);

        // Fallback for history
        if ('history' === this.options.mode) {
            fragment = this.clearSlashes(decodeURI(location.pathname + location.search));
            fragment = fragment.replace(/\?(.*)$/, '');
            fragment = this.root != '/' ? fragment.replace(this.root, '') : fragment;
        }

        return this.clearSlashes(fragment);
    },


    /**
     * Add Route
     */
    add: function (route, callback) {
        this.options.routes.push({
            route: route,
            callback: callback
        });

        return this;
    },


    /**
     * Route current URI to a route and run the handler
     */
    route: function () {
        var fragment = this.getFragment();

        /**
         * Avoid duplicate runs
         */
        if (this.options.previousMatch === fragment) {
            return this;
        }
        this.options.previousMatch = fragment;

        /**
         * Loop routes
         */
        this.options.routes.each(function (route) {
            /**
             * Match with regex
             */
            var match = fragment.match(route.route);

            if (match) {
                match.shift();

                /**
                 * Run callback
                 */
                route.callback.apply({}, match);

                return this;
            }
        }, this);

        return this;
    },


    /**
     * Navigate to URI
     */
    nav: function(path) {
        if ('history' === this.options.mode) {
            history.pushState(null, null, this.options.root + this.clearSlashes(path))

            return this.route();
        }

        window.location.hash = '#!' + this.clearSlashes(path);

        return this;
    },


    /**
     * Removes slashes at start and end
     */
    clearSlashes: function(path) {
        return path.toString().replace(/\/$/, '').replace(/^\//, '');
    }
});
