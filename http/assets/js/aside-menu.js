'use strict';

if ($('show-aside-button')) {
    /**
     * Change aside margin to show it
     * Change content width do adjust to fit on the side of aside
     */
    var showAside = (function() {
        $('aside-menu').setStyle('margin-left', '0rem');
        $('content').setStyle('width', 'calc(100% - 17rem)');
    });



    /**
     * Change aside margin to hide it
     * Change content width do adjust to fit on the side of aside
     */
    var hideAside = (function() {
        $('aside-menu').setStyle('margin-left', '-18rem');
        $('content').setStyle('width', '100%');
    });



    /**
     * Open and close button for aside-menu
     */
    $('show-aside-button').addEvent('click', function (event) {
        // Get the aside current margin-left as integer
        var asideMargin = parseInt($('aside-menu').getStyle('margin-left'));

        if (0 === asideMargin) {
            hideAside();

            return true;
        }

        showAside();
    });



    /**
     * Catch all clicks on the content element, then check if we should close the
     * aside or not.
     */
    $('content').addEvent('click', function (event) {
        var asideButtonVisible = $('show-aside-button').getStyle('display') !== 'none';
        var asideVisible = parseInt($('aside-menu').getStyle('margin-left')) === 0;

        if (false === asideButtonVisible && false === asideVisible) {
            showAside();
        }

        if (true === asideButtonVisible && true === asideVisible) {
            hideAside();
        }
    });
}
