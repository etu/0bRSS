'use strict';

if ($('header-menu-button')) {
    /**
     * Open and close button for menu
     */
    $('header-menu-button').addEvent('click', function (event) {
        $('header-menu').setStyle(
            'display',
            $('header-menu').getStyle('display') === 'none' ? 'block' : 'none'
        );
    });



    /**
     * Catch all clicks on the html element (everything basicly), then filter out
     * which is not interesting (menu button and menu itself) and then close the
     * menu if it's not catched in the not interesting filter
     */
    $$('html')[0].addEvent('click', function (event) {
        if (event.target                           === $('header-menu-button') ||
            event.target.getParent('#header-menu') === $('header-menu') ||
            event.target                           === $('header-menu')) {
            return true;
        }

        if ('block' === $('header-menu').getStyle('display')) {
            $('header-menu').setStyle('display', 'none');
        }
    });
}
