<?php
// 检查登录状态
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// 重定向
function redirect($url) {
    header("Location: $url");
    exit;
}

// 获取API站点配置
function getApiSite($source, $apiSites) {
    return isset($apiSites[$source]) ? $apiSites[$source] : null;
}

// 发送API请求
function sendApiRequest($url, $isPost = false, $postData = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
    
    if ($isPost && $postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return $response;
    }
    
    return false;
}

// 输出JSON响应
function jsonResponse($data, $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function handleError($errno, $errstr, $errfile, $errline) {
    Logger::error("Error [$errno] $errstr in $errfile on line $errline");
    
    if (error_reporting() === 0) {
        return false;
    }
    
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    switch ($errno) {
        case E_USER_ERROR:
            http_response_code(500);
            echo json_encode([
                'code' => 500,
                'msg' => '服务器内部错误'
            ]);
            exit(1);
            break;
            
        case E_USER_WARNING:
            Logger::error("WARNING: $errstr");
            break;
            
        case E_USER_NOTICE:
            Logger::info("NOTICE: $errstr");
            break;
            
        default:
            Logger::debug("Unknown error type: [$errno] $errstr");
            break;
    }
    
    return true;
}

// 设置错误处理函数
set_error_handler('handleError'); 