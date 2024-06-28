<?php

    
    $error ='';
    $success_message = '';
    
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        session_start();

        if(isset($_SESSION['user_data']))
        {
            require_once('database/ChatUser.php');
            
            $user = new ChatUser();
        
            $user-> setRegistrationEmail($_SESSION['user_data']['email']);

            $userData = $user->getUserByEmail();
            
            
            if(is_array($userData) && count($userData) > 0)
            {
                if(password_verify($_POST['opassword'],$userData['password']))
                {
                        $user-> setPassword($_POST['npassword']);
                        if ($user->resetPassword()) {
                            $success_message = "Password updated ! Enjoy your safe & secure chatting :)";
                        } else {
                            $error =  "Error: " . $db->errorInfo()[2];
                        }
                }
                else
                {
                    $error = "Are you confident you remember your password?";
                }
            }
      }
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password recovery</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="container log-container">
    <?php
            if($error != '')
            {
                echo '<div class="alert alert-danger" role="alert">
                '.$error.'
                </div>';
            }
            if($success_message != '')
            {
                echo '<div class="alert alert-success" role="alert">
                '.$success_message.'
                </div>';
            }
        ?>
        <div class="title">Password recovery</div>
        <div class="content">
            <form method="POST" onsubmit="return validateForm()">
                <div class="user-details fileds">
                    <div class="input-box">
                        <span class="details">Old Password</span>
                        <input type="text" placeholder="Enter your old password" id="old-password" name="opassword" required>
                        <div id="oldPasswordError" class="error-message"></div>
                    </div>
                    <div class="input-box">
                        <span class="details">New Password</span>
                        <input type="text" placeholder="Enter your new password" id="new-password" name="npassword" required>
                        <div id="newPasswordError" class="error-message"></div>
                    </div>
                    <div class="input-box">
                        <span class="details">Confirm New Password</span>
                        <input type="text" placeholder="Confirm your new password" id="cnew-password" name="cnpassword" required>
                        <div id="confError" class="error-message"></div>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" value="Submit">
                </div>

            </form>
        </div>
    </div>


    <script>
        function validateForm() {
            // event.preventDefault();
            console.log("hleo");
           
            var oldPassword = document.getElementById('old-password').value;
            var newPassword = document.getElementById('new-password').value;
            var confirmPassword = document.getElementById('cnew-password').value;


            // Validate password and confirm password match
        
            var passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            var errorMessage = "Password must contain at least one letter, one number, one special character, and be at least 8 characters long.";

            // Check if password matches the regex
            if (!passwordRegex.test(oldPassword)) {
                document.getElementById('oldPasswordError').textContent = errorMessage;
                return false;
            } 

            if (!passwordRegex.test(newPassword)) {
                document.getElementById('newPasswordError').textContent = errorMessage;
                return false;
            } 

            var errorMessage = "Password and Confirm Password do not match";
            if (newPassword!== confirmPassword) {
                document.getElementById('confError').textContent = errorMessage;
                return false;
            }

            return true;
        }
    </script> 



</body>

</html>