<?php
session_start(); 

$valid_username = "blog";
$valid_password = "blog2005";

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php"); 
    exit();
}

if (isset($_POST['login'])) {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    if ($input_username === $valid_username && $input_password === $valid_password) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $input_username;

        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid username or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
         :root {
  --purple: #3B3646;
  --red: #EE4B5A;
}

#gdpr-cookie-message {
  position: fixed;
  right: 30px;
  bottom: 30px;
  max-width: 550px;
  background-color: var(--purple);
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 6px 6px rgba(0, 0, 0, 0.25);
  margin-left: 30px;
  font-family: system-ui;
  z-index: 1000;
}

#gdpr-cookie-message h4 {
  color: var(--red);
  font-family: 'Quicksand', sans-serif;
  font-size: 18px;
  font-weight: 500;
  margin-bottom: 10px;
}

#gdpr-cookie-message p {
  color: white;
  font-size: 15px;
  line-height: 1.5em;
}

#gdpr-cookie-message p:last-child {
  margin-bottom: 0;
  text-align: right;
}

#gdpr-cookie-message a {
  color: var(--red);
  text-decoration: none;
  font-size: 15px;
  padding-bottom: 2px;
  border-bottom: 1px dotted rgba(255, 255, 255, 0.75);
  transition: all 0.3s ease-in;
}

#gdpr-cookie-message a:hover {
  color: white;
  border-bottom-color: var(--red);
  transition: all 0.3s ease-in;
}

#gdpr-cookie-message button {
  border: none;
  background: var(--red);
  color: white;
  font-family: 'Quicksand', sans-serif;
  font-size: 15px;
  padding: 7px;
  border-radius: 3px;
  margin-left: 15px;
  cursor: pointer;
  transition: all 0.3s ease-in;
}

#gdpr-cookie-message button:hover {
  background: white;
  color: var(--red);
  transition: all 0.3s ease-in;
}

        .login-container {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 300px;
        }

        .login-container h2 {
            text-align: center;
            color: #333;
        }

        .login-form label {
            display: block;
            margin-bottom: 8px;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 9px;
        }

        .login-form input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .login-form input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRuld8t1Wry-iPatHLQP0K_kHBZka111VBYaw&s" height="150" width="150"></h2>
        <form class="login-form" method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" name="login" value="Login">
        </form>
        <div class="error-message">
            <?php
            if (isset($error_message)) {
                echo $error_message;
            }
            ?>
        </div>
    </div>
    <div id="gdpr-cookie-message" style="display: block;">
  <h4>Privacy consent</h4>
  <p>
 The information available on this page is safeguarded with complete security and safety. We prioritize user privacy, we assuring you that your personal information is handled with the utmost care, to provide you with a secure and safe online experience.
  </p>
  <p>
    <a href="https://www.privacypolicies.com/live/a3b77bf9-4b4a-4deb-a7d0-732f3fd5e9e0">More information</a>
    <button id="gdpr-cookie-accept" type="button" onclick="closeCookieMessage()">Accept</button>
  </p>
</div>

<script>
  function closeCookieMessage() {
    var cookieMessage = document.getElementById("gdpr-cookie-message");
    cookieMessage.style.display = "none";
  }
</script>
</body>
</html>
