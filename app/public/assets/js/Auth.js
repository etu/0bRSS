'use struct';

class Auth {
    constructor() {
        this.token = localStorage.getItem('token');

        if (this.token === null) {
            return this.drawLoginArea();
        }
    }

    drawLoginArea() {
        document.getElementById('content').innerHTML = `
          <form id="loginform">
            <fieldset>
              <input type="email" name="email" placeholder="Email" required />
              <input type="password" name="password" placeholder="Password" required />
              <button id="login-button">Login</button>
            </fieldset>
          </form>
        `;
    }
}

window.ZerobRSS.Auth = new Auth();
