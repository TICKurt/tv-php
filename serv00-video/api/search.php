<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// 需要登录才能访问API
if (!isLoggedIn()) {
    jsonResponse(['code' => 401, 'msg' => '未授权访问'], 401);
}

// 获取搜索关键词
$searchQuery = $_GET['wd'] ?? '';
if (empty($searchQuery)) {
    jsonResponse(['code' => 400, 'msg' => '搜索词不能为空', 'list' => []]);
}

// 获取数据源
$source = $_GET['source'] ?? 'hm';
$customApi = $_GET['customApi'] ?? '';

// 如果是自定义API
if ($source === 'custom' && !empty($customApi)) {
    $apiUrl = $customApi . '/api.php/provide/vod/?ac=list&wd=' . urlencode($searchQuery);
} else {
    // 从配置获取API站点
    $site = getApiSite($source, $API_SITES);
    if (!$site) {
        jsonResponse(['code' => 400, 'msg' => '未找到有效的数据源', 'list' => []]);
    }
    
    $apiBase = isset($site['api']) ? $site['api'] : str_replace('/index.php/vod/search.html', '', $site['url']);
    $apiUrl = $apiBase . '/api.php/provide/vod/?ac=list&wd=' . urlencode($searchQuery);
}

// 发送请求
$response = sendApiRequest($apiUrl);
if (!$response) {
    jsonResponse(['code' => 400, 'msg' => '搜索服务暂时不可用，请稍后再试', 'list' => []]);
}

// 解析响应
$data = json_decode($response, true);
if (!$data || !isset($data['list'])) {
    jsonResponse(['code' => 400, 'msg' => '获取数据失败，请稍后再试', 'list' => []]);
}

// 返回结果
jsonResponse($data); 