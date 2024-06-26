<?php
session_start();
if (!isset($_SESSION['user_data'])) {
    header('location:index.php');
}

$user_obj = $_SESSION['user_data'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatApp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style/dashboard.css">
</head>

<body>
    <section id="dashboard">
        <div class="container">
            <div class="navbar">
                <h1 class="logo">ChatApp</h1>
                <?php

                $login_user_id = $user_obj['id'];
                require_once 'database/ChatUser.php';
                $chatuser = new ChatUser();
                $chatuser->setUserId($login_user_id);
                $user_data = $chatuser->getAllUsersDataWithStatus();

                ?>
                <div class="profile">
                    <p>
                        <a href="profile.php">
                            <?php echo $user_obj['username'] ?>
                        </a>
                    </p>
                    <span>
                        <img src="./assets/avatar.png" alt="avatar">
                    </span>
                    <input type="hidden" id="login_user_id" name="login_user_id" value="<?php echo $login_user_id ?>">
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a id="logout" onclick="logoutUser()">Logout</a>
                    </div>
                </div>

            </div>
            <div class="user-chat-box">
                <div class="users-box">
                    <?php
                    foreach ($user_data as $key => $user) {
                        if ($user['user_id'] != $login_user_id) {
                            echo "
                            <div class='user-text-box' id='chat11_user'  data-userid = '" . $user['user_id'] . "' onclick='loadChat()'>
                                <div class='profile'>
                                    <img src='./assets/avatar.png' alt='avatar'>
                                </div>
                                <div class='text-box'>
                                    <p class='username-box' id='list_user_name_" . $user['user_id'] . "'>" . $user['fname'] . ' ' . $user['lname'] . "</p>
                                    <p class='status-box' id='list_user_status_" . $user['user_id'] . "'>" . $user['status'] . "</p>
                                </div>
                            </div>
                        ";
                        }

                    }
                    ?>
                </div>
                <div class="chat-box" id="chatpart">

                </div>
            </div>
        </div>
    </section>
</body>
<script>
    var receiver_userid = '';
    document.addEventListener('DOMContentLoaded', () => {
        const profileIcon = document.querySelector('.profile span');
        const dropdownContent = document.querySelector('.dropdown-content');

        function toggleDropdown(event) {
            event.stopPropagation();
            dropdownContent.classList.toggle('show');
        }

        function closeDropdown(event) {
            if (!profileIcon.contains(event.target) && dropdownContent.classList.contains('show')) {
                dropdownContent.classList.remove('show');
            }
        }

        profileIcon.addEventListener('click', toggleDropdown);
        document.addEventListener('click', closeDropdown);



    });
    function logoutUser() {
        var userId = document.getElementById("login_user_id").value;
        console.log(userId);
        if (userId) {
            fetch('action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'user_id': userId,
                    'action': 'leave'
                })
            })
                .then(response => response.text())
                .then(data => {
                    console.log("Response received: " + data);
                    let response;
                    try {
                        response = JSON.parse(data);
                    } catch (e) {
                        console.log("Failed to parse JSON response: " + e);
                        return;
                    }

                    if (response.status == 1) {
                        console.log("Logout successful, redirecting...");
                        location.href = "index.php";
                    } else {
                        console.log("Logout failed");
                    }
                })
                .catch(error => {
                    console.error("Fetch Error: " + error);
                });
        } else {
            console.warn("User ID not found");
        }

    }
    function make_chat_area(user_name, user_status) {
        var htmlcode = `
                    <div class="chat-navbar user-text-box">
                        <div class="profile">
                            <img src="./assets/avatar.png" alt="avatar">
                        </div>
                        <div class="text-box">
                            <p class="username-box">`+ user_name + `</p>
                            <p class="status-box">`+ user_status + `</p>
                        </div>
                    </div>
                    <div class="chat-content">
                        <div class="chat-text-box" id="message_text_box">
                            <div class="receiver-message">
                                <p>
                                    hii how are you
                                    hii how are you
                                    hii how are you
                                    hii how are you
                                </p>
                                <span>12:49 pm</span>
                            </div>
                            <div class="sender-message">
                                <p>
                                    Fine
                                </p>
                                <span>12:49 pm</span>
                            </div>
                        </div>
                    </div>

                    <div class="chat-message-box">
                    <form method="POST" onsubmit="event.preventDefault(); handleMessage();">
                        <input type="text" id="user_text_message" placeholder="Type a message...">
                        <button type="submit" ><span><i class="fa fa-send-o"></i></span></button>
                    </form>
                    </div>
                </div>
        `;
        document.getElementById('chatpart').innerHTML = htmlcode;
    }

    function loadChat() {
        receiver_userid = document.getElementById('chat11_user').getAttribute('data-userid');
        var userId = document.getElementById('login_user_id').value;
        var receiver_name = document.getElementById('list_user_name_' + receiver_userid).innerHTML;
        var receiver_status = document.getElementById('list_user_status_' + receiver_userid).innerHTML;
        console.log(userId,receiver_userid);
        make_chat_area(receiver_name, receiver_status);

        fetch('action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'to_user_id': receiver_userid,
                'from_user_id': userId,
                'action': 'fetch_chat',
            })
        })
            .then(response => response.text())
            .then(data => {
                console.log("Response received: " + data);
                let response;
                try {
                    response = JSON.parse(data);
                } catch (e) {
                    console.log("Failed to parse JSON response: " + e);
                    return;
                }
                if(response.length > 0)
                {
                    var html_data = '';
                    for(var count =0;count < response.length;count++)
                    {
                        if(response[count].sender_id == userId)
                        {
                            html_data += `<div class="sender-message">
                                <p>
                                    `+response[count].message+`
                                </p>
                                <span>`+response[count].timestamp+`</span>
                            </div>`
                        }
                        else
                        {
                            html_data +=`
                            <div class="receiver-message">
                                <p>
                                    `+response[count].message+`
                                </p>
                                <span>`+response[count].timestamp+`</span>
                            </div>
                            `
                        }
                        
                    }
                    document.getElementById('message_text_box').innerHTML+=html_data;
                }
                
            })
            .catch(error => {
                console.error("Fetch Error: " + error);
            });

    }
    function handleMessage()
    {
        
        var inputmsg =document.getElementById('user_text_message');
        var message = inputmsg.value.trim();
        var receiver_userid = document.getElementById('chat11_user').getAttribute('data-userid');
        var userId = document.getElementById('login_user_id').value;
        console.log(receiver_userid,userId);
        fetch('action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'to_user_id': receiver_userid,
                'from_user_id': userId,
                'message':message,
                'action': 'send_message',
            })
        })
            .then(response => response.text())
            .then(data => {
                console.log("Response received: " + data);
                let response;
                try {
                    response = JSON.parse(data);
                } catch (e) {
                    console.log("Failed to parse JSON response: " + e);
                    return;
                }
                if( response && parseInt(response.status) > 0)
                {
                    var html_data = '';
                    
                    html_data += `<div class="sender-message">
                        <p>
                            `+message+`
                        </p>
                        <span>`+response.timestamp+`</span>
                    </div>`
                       
                    document.getElementById('message_text_box').innerHTML+=html_data;
                }
                
            })
            .catch(error => {
                console.error("Fetch Error: " + error);
            });
        inputmsg.value='';
    }
</script>

</html>