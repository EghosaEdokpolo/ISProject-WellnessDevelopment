<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Meta data about the page that isn't seen -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="group 1">
        <meta name="description" content="Contains the login page credentials in order to access the strathmore wellness website. Affiliated with Strathmore University">
        
        <!-- Laravel Vite directive to load compiled application bundles if needed -->
        @vite(['resources/css/style.css', 'resources/js/app.js'])

        <!-- Favicon -->
            <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">


        <!-- 
          THE DEDICATED STYLESHEET
          The asset() helper targets the 'public/' folder. 
          This line maps cleanly to: public/css/style.css 
        -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        
        <title>Strathmore wellness-Login</title>
    </head>

    <body>
        <!-- Data about the page that is seen -->

        <!-- Strathmore Image Wrapper using the public/ images folder asset -->
        <div id="loginstrathlogo">
            <a href="https://strathmore.edu">
                <img src="{{ asset('images/Strathmore_uni_logo-text.png') }}" alt="Picture of Strathmore University Logo" title="Strathmore University Logo">
            </a>
        </div>

        <!-- Main Login Box Structure using your dedicated #loginform ID layout rules -->
        <div id="loginform">
            <h1>Log In To Your Account!</h1>

            <!-- 
              ERROR ALERT FEEDBACK
              If login validation fails, this block pops up to display the error text.
              You can style the class "error-box" inside your public/css/style.css file!
            -->
            @if ($errors->any())
                <div class="error-box" style="color: #b91c1c; background-color: #fef2f2; border: 1px solid #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                    <ul style="list-style: inside; margin: 0; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 
              FORM ACTIONS
              Submits data securely to the Laravel Auth Controller using POST 
            -->
            <form action="{{ route('login') }}" method="POST">
                <!-- 
                  @csrf Token: Laravel security verification.
                  Mandatory for handling local database form submissions.
                -->
                @csrf

                <h2>Staff ID Number</h2>
                <label for="staff_id"></label>
                <!-- name updated to 'staff_id' to speak with the backend login request rules -->
                <input type="text" name="staff_id" id="staff_id" required placeholder="000000" value="{{ old('staff_id') }}">

                <h2>Password</h2>
                <label for="password"></label>
                <input type="password" name="password" id="password" required>
                <br>
                
                <!-- Uses your dedicated .loginbutton styles from style.css -->
                <button class="loginbutton" type="submit">Login</button>

                <br>
                <a href="https://su-sso.strathmore.edu/staff-pss/private/login" target="_blank" rel="noopener noreferrer">Forgotten your credentials?</a>
            </form>
        </div>

        <!-- Global Base Footer -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} Strathmore - All Rights Reserved</p> 
        </footer>
    </body>
</html>
