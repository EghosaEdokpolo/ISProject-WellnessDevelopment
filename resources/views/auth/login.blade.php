<!DOCTYPE html>
<html lang="en">
    <head>
        <!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="desription" content="Contains the login page credentials in order to access the strathmore wellness website. Affiliated with Strathmore University">
        
        <!--Favicon-->
        <link rel="icon" href="assets/Strathmore_uni_logo-notext.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        
        <title>Strathmore wellness-Login</title>
    </head>

    <body>
        <!--data about the page that is seen-->

        <!--strathmore Image-->
        <div id="loginstrathlogo">
            <a href="https://strathmore.edu">
                <img  src="images/Strathmore_uni_logo-text.png" alt="Picture of Strathmore University Logo" title="Strathmore University Logo">
            </a>
        </div>

        <!--Title-->
        <div id="loginform">
            <h1>Log In To Your Account!</h1>
            <form id="loginForm">
    <h2>Staff ID Number</h2>
    <label for="number"></label>
    <input type="text" name="staff_id_number" id="number" required placeholder="000000">

    <h2>Password</h2>
    <label for="password"></label>
    <input type="password" name="password" id="password" required>
    <br>
    
    <button class="loginbutton" type="submit" id="loginBtn">Login</button>
    <p id="loginError" style="color:#ffb3b3; display:none; text-align:center;"></p>

    <br>
    <a href="https://su-sso.strathmore.edu/staff-pss/private/login">Forgotten your credentials? </a>
</form>

<script type="module">
import { setToken } from '/js/api.js';

const form = document.getElementById('loginForm');
const errorEl = document.getElementById('loginError');
const btn = document.getElementById('loginBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorEl.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Logging in...';

    try {
        const res = await fetch('/api/v1/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                staff_id_number: document.getElementById('number').value,
                password: document.getElementById('password').value,
                device_name: 'web',
            }),
        });

        if (!res.ok) {
            const err = await res.json();
            throw new Error(err.message || 'Invalid credentials');
        }

        const data = await res.json();
        setToken(data.token);

        // Redirect to overview
        window.location.href = '/hrdb_overview.html';
    } catch (err) {
        errorEl.textContent = err.message;
        errorEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Login';
    }
});
</script>
        </div>

        <!-- <hr> -->
        <footer class="footer">
            <p>&copy; Strathmore - All Rights Reserved</p> 
        </footer>
    </body>
</html>