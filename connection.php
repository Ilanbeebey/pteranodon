<?php

if (session_status() == PHP_SESSION_NONE) {

    $lifetime = 0;

    $path = '/';

    $domain = "weblab.beebey.ml";

    $secure = true;

    $httponly = true;

    

    session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);



    session_start();

}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli('localhost', 'pteranodon', 'ultrasafepass', 'pteranodon', NULL, '/run/mysqld/mysqld.sock');
} catch(Exception $e) {

    exit();

    die('Erreur');

}

function check_login(){

    if (!empty($_SESSION['id']) AND !empty($_SESSION['username']) AND !empty($_SESSION['password'])) {

        return true;

		

    } else {

		

        return false;

    }

}

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED);

date_default_timezone_set('Europe/Brussels');

$domaine = 'https://'.$_SERVER['HTTP_HOST'].'/';

if (!isset($_SESSION['id']) AND !isset($_SESSION['password']) AND !empty($_COOKIE['remember'])) {

    $remember = json_decode(openssl_decrypt($_COOKIE['remember'], 'AES-128-ECB', "beebeyttropbgwtfmec"), true);

    if (!empty($remember['id']) AND !empty($remember['password'])) {

        $req = $db->prepare('SELECT * FROM users WHERE id = ? AND password = ?');

        $req->bind_param("is", $remember['id'], $remember['password']);
        $req->execute();

        if ($req->num_rows == 1) {

            $r = $req->fetch();

            

            $_SESSION['id'] = $r['id'];

            $_SESSION['username'] = $r['username'];

            $_SESSION['password'] = $r['password'];

        }
        else
        {
            $req->free_result();
        }

    }

}

if (!isset($_SESSION['token'])) {

    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));

}

if (!empty($disconnect)) {

    $_SESSION = array();

    session_destroy();

    setcookie('remember', null, -1, '/', $_SERVER['HTTP_HOST'], true, true);

    header('Location: /login.php');

    exit();

}

?>