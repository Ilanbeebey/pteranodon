<?php

require('connection.php');

if(check_login()){
    $_SESSION = array();
    session_destroy();
    setcookie('remember', null, -1, '/', $_SERVER['HTTP_HOST'], true, true);
} else {
    header('Location: /login.php');
}

?>