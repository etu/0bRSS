'use struct';

class Auth {
    constructor() {
        this.token = localStorage.getItem('token');

        if (this.token === null) {
            return this.drawLoginArea();
        }
    }

    drawLoginArea() {
        var content = document.getElementById('content');

        content.innerHTML = `
          <form id="loginform">
            <fieldset>
              <input type="email" name="email" placeholder="Email" required />
              <input type="password" name="password" placeholder="Password" required />
              <button id="login-button">Login</button>
            </fieldset>
          </form>
        `;

        // Draw the login box
        content.classList.add('fullwidth');

        // Hide the sidebar menu
        document.getElementById('aside-menu').style.display = 'none';

        // Hide the sidebar show/hide button
        document.getElementById('show-aside-button').style.display = 'none';

        // Hide the right hand side menu button
        document.getElementById('header-menu-button').style.display = 'none';
    }
}

window.ZerobRSS.Auth = new Auth();
