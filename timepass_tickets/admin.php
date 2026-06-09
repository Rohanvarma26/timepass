<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            background-color: #141414;
            color: white;
            font-family: 'Open Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-wrapper {
            max-width: 400px;
            background: #222;
            padding: 30px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            text-align: center;
        }
        h1 {
            font-size: 28px;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 20px;
        }
        .form-group {
            margin: 15px 0;
            text-align: left;
        }
        .form-group label {
            font-weight: bold;
            color: #e0e0e0;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background-color: #333;
            color: white;
            border: 1px solid #555;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }
        .form-group input:focus {
            border-color: #e50914;
        }
        .error-message {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 5px;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #e50914;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .login-btn:hover {
            background-color: #ff1e22;
        }
    </style>
    <script type="text/javascript">
        function validation() {
            var mail = document.getElementById('txtemail').value;
            var password = document.getElementById('txtpwd').value;
            var isValid = true;

            document.getElementById('error').innerHTML = "";
            document.getElementById('errorpass').innerHTML = "";

            if (mail == "") {
                document.getElementById('error').innerHTML = "*Please enter Email ID";
                isValid = false;
            }
            if (password == "") {
                document.getElementById('errorpass').innerHTML = "*Please enter Password";
                isValid = false;
            }
            return isValid;
        }
    </script>
</head>

<body>
    <div class="login-wrapper">
        <h1>Admin Login</h1>
        <form action="" method="post" onsubmit="return validation()">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="txtemail" id="txtemail" placeholder="Enter Admin Email">
                <div id="error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="txtpwd" id="txtpwd" placeholder="Enter Password">
                <div id="errorpass" class="error-message"></div>
            </div>
            <button class="login-btn" type="submit" name="btnsubmit">Sign In</button>
        </form>
    </div>
</body>
</html>
