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
          <div id="errormessage"></div>
          <form id="loginform" action="javascript:window.ZerobRSS.Auth.submitForm();">
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

        // Hide error message element
        document.getElementById('errormessage').style.display = 'none';
    }

    async submitForm() {
        var form = document.getElementById('loginform');

        // Submit form
        var response = await fetch(window.ZerobRSS.apiUri + '/v1/login', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json; charset=UTF-8',
            },
            body: JSON.stringify({
                email: form.getElementsByTagName('input').email.value,
                password: form.getElementsByTagName('input').password.value,
            }),
            method: 'POST',
        });

        // Parse response
        var json = await response.json();

        // We got token
        if (json.token) {
            // Store token
            localStorage.setItem('token', json.token);

            // Reload page
            location.reload();
            return;
        }

        // Show error message
        document.getElementById('errormessage').style.display = 'block';
        document.getElementById('errormessage').innerHTML = json.message;
    }

    async logout() {
        // Build API URI
        var uri = window.ZerobRSS.apiUri + '/v1/logout?token=' + window.ZerobRSS.Auth.token;

        // Do request
        var response = await fetch(uri, {
            headers: {
                'Accept': 'application/json',
            },
        });

        // Remove access token
        localStorage.removeItem('token');

        // Reload page
        location.reload();
    }
}

window.ZerobRSS.Auth = new Auth();
