<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NovaTech Login</title>

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Loginpage.php');
    exit();
}

    <link rel="preload" href="Assets/Home/login_banner.jpg" as="image" />
</head>
<body>

// basic check
if ($identifier === '' || $password === '') {
    header('Location: Loginpage.php?error=wrong');
    exit();
}

    <div class="container">
        <div class="left"></div>

        <div class="right">
            <img src="Assets/Home/Logo.png" class="logo" />

            <h1>Welcome Back</h1>
            <p>Log in to access NovaTech services.</p>

            <!-- server error from login.php -->
            <p id="serverError" style="display:none;" class="auth-info"></p>

            <!-- js error -->
            <!-- js error -->
            <p id="infolabel" hidden class="auth-info"></p>


            <form action="login.php" method="post" id="loginForm">

                <div class="form-group">
                    <label>Username or Email</label>
                    <input
                        type="text"
                        id="username"
                        name="identifier"
                        placeholder="Enter your username or email"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    />
                </div>

                <input type="hidden" name="submitted" value="true" />

                <button type="submit">Login</button>
            </form>

            <p class="login-text">
                Don't have an account?
                <a href="Register.php">Register</a>
            </p>
        </div>
    </div>

    <script>
        // shows error message 
        const params = new URLSearchParams(window.location.search);
        if (params.get("error") === "wrong") {
            const msg = document.getElementById("serverError");
            msg.textContent = "Incorrect username/email or password.";
            msg.style.display = "block";
        }
    </script>
</body>
</html>
