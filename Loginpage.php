<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NovaTech Login</title>

    <script src="javascript/Login.js"></script>
    <link rel="stylesheet" href="Styles/Login.css" />
    <link rel="preload" href="Assets/Home/login_banner.jpg" as="image" />
</head>
<body>

    <a href="Homepage.php" id="back-home-link">
        <div class="back-home-container">
            <img src="Assets/Home/arrow.png" class="home-icon" alt="Back" />
            <span class="back-home-text">Back to Home</span>
        </div>
    </a>

    <div class="container">
        <div class="left"></div>

        <div class="right">
            <img src="Assets/Home/Logo.png" class="logo" alt="NovaTech Logo" />

            <h1>Welcome Back</h1>
            <p>Log in to access NovaTech services.</p>

            <!-- error / success message from server -->
            <p id="serverError" style="display:none;" class="auth-info"></p>

            <!-- message label used by existing JS -->
            <p id="infolabel" hidden class="auth-info"></p>

            <form action="loginb.php" method="post" id="loginForm">
                <div class="form-group">
                    <label>Full Name or Email</label>
                    <input
                        type="text"
                        id="username"
                        name="identifier"
                        placeholder="Enter your full name or email"
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

            <p class="login-text forgot-password-text">
                <a href="forgot_password.php">Forgot your password?</a>
            </p>

            <p class="login-text register-text">
                Don't have an account?
                <a href="register.html">Register</a>
            </p>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const msg = document.getElementById("serverError");

        if (params.get("error") === "wrong") {
            msg.textContent = "Incorrect full name/email or password.";
            msg.style.display = "block";
        }

        if (params.get("error") === "empty") {
            msg.textContent = "Please fill in all fields.";
            msg.style.display = "block";
        }

        if (params.get("reset") === "success") {
            msg.textContent = "Password reset successful. You can now log in.";
            msg.style.display = "block";
        }
    </script>
</body>
</html>