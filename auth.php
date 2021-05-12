<?php

function logoutHandler() 
{
    session_start();
    $params = session_get_cookie_params();
    setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
    session_destroy();
    header('Location: /');
}

function redirectToLoginPageIfNotLoggedIn()
{
    if (isLoggedIn()) {
        return;
    }

    header('Location: /admin');
    exit;
}



function loginHandler()
{
    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    if (!$user) {
        header('Location: /admin?info=invalidCredentials');
        return;
    }

    if (!password_verify($_POST['password'], $user['password'])) {
        header('Location: /admin?info=invalidCredentials');
        return;
    }

    session_start();
    $_SESSION['userId'] = $user['id'];
    header('Location: /admin');
}

// https://kodbazis.hu/php-az-alapoktol/bejelentkezesi-felulet-es-felhasznalo-kezeles
function isLoggedIn()
{
    if (!isset($_COOKIE[session_name()])) {
        return false;
    }

    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['userId'])) {
        return false;
    }

    return true;
}