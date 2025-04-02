<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// 验证用户登录
function authenticateUser($username, $password) {
    // 简单登录验证 (实际应用中应使用数据库和密码哈希)
    if ($username === DEFAULT_USERNAME && $password === DEFAULT_PASSWORD) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        return true;
    }
    return false;
}

// 注销用户
function logoutUser() {
    session_unset();
    session_destroy();
}

// 需要登录的页面请在页面顶部调用此函数
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
} 