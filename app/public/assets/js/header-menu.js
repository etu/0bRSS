'use strict';

var menuButton = document.getElementById('header-menu-button');
var menu = document.getElementById('header-menu');

if (menuButton) {
    // Make sure menu is hidden
    menu.style.display = 'none';

    // Toggle showing of menu
    menuButton.onclick = function() {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    };

    // Hide menu when clicking outside of the menu
    document.getElementsByTagName('html')[0].onclick = function (event) {
        if (event.target === document.getElementById('header-menu-button') ||
            event.target === document.getElementById('header-menu') ||
            event.target.parentNode === document.getElementById('header-menu')) {
            return true;
        }

        if (menu.style.display === 'block') {
            menu.style.display = 'none';
        }
    };

    document.getElementById('header-menu-logout').onclick = function (event) {
        window.ZerobRSS.Auth.logout();
    };
}
