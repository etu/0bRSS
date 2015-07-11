'use strict';

/**
 * Open and close button for aside-menu
 */
$('show-aside-button').addEvent('click', function (event) {
    // Get the aside current margin-left as integer
    var asideMargin = parseInt($('aside-menu').getStyle('margin-left'));

    /**
     * Change aside margin to show or hide it
     */
    $('aside-menu').setStyle(
        'margin-left',
        asideMargin === 0 ? '-18rem' : '0rem'
    );

    /**
     * Change content width do adjust to fit on the side of aside
     */
    $('content').setStyle(
        'width',
        asideMargin === 0 ? '100%' : 'calc(100% - 17rem)'
    );
});
