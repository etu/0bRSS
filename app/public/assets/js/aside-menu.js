'use strict';

var menuButton = document.getElementById('show-aside-button');

if (menuButton) {
    /**
     * Change aside margin to show it
     * Change content width do adjust to fit on the side of aside
     */
    var showAside = (function () {
        document.getElementById('aside-menu').style.marginLeft = '0rem';
        document.getElementById('content').style.marginRight = '-17rem';
    });


    /**
     * Change aside margin to hide it
     * Change content width do adjust to fit on the side of aside
     */
    var hideAside = (function () {
        document.getElementById('aside-menu').style.marginLeft = '-17rem';
        document.getElementById('content').style.marginRight = '0rem';
    });



    /**
     * Open and close button for aside-menu
     */
    menuButton.onclick = (function (event) {
        var asideMargin = document.getElementById('aside-menu').style.marginLeft;

        // set a fallback value
        if (asideMargin === '') {
            asideMargin = 1;
        }

        // Hide aside if it's showing
        if (parseInt(asideMargin) === 0) {
            hideAside();

            return true;
        }

        // Otherwise show the aside
        showAside();
    });
}
