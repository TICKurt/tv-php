<?php
session_start();

// 基础配置
define('APP_NAME', '视频搜索系统');
define('APP_ROOT', dirname(__DIR__));
define('CONFIG_PATH', APP_ROOT . '/configs');

// 默认用户凭据 (实际应用中应使用数据库存储)
define('DEFAULT_USERNAME', 'movie');
define('DEFAULT_PASSWORD', 'movie');

// 加载视频源配置
$webJsonPath = CONFIG_PATH . '/web.json';
$API_SITES = [];

if (file_exists($webJsonPath)) {
    $webJsonContent = file_get_contents($webJsonPath);
    $sites = json_decode($webJsonContent, true);
    
    // 转换web.json为程序可用格式
    foreach ($sites as $site) {
        if (isset($site['remark']) && $site['active']) {
            $key = strtolower($site['remark']);
            $API_SITES[$key] = $site;
        }
    }
} else {
    die("配置文件不存在");
} 