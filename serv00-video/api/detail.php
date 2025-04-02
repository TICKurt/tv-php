<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// 需要登录才能访问API
if (!isLoggedIn()) {
    jsonResponse(['code' => 401, 'msg' => '未授权访问'], 401);
}

// 获取视频ID
$id = $_GET['id'] ?? '';
if (empty($id)) {
    jsonResponse(['code' => 400, 'msg' => '视频ID不能为空']);
}

// 获取数据源
$source = $_GET['source'] ?? 'hm';
$customApi = $_GET['customApi'] ?? '';

// 构建详情页URL
if ($source === 'custom' && !empty($customApi)) {
    $baseUrl = preg_replace('/\/api\.php.*$/', '', $customApi);
    $detailUrl = "$baseUrl/index.php/vod/detail/id/$id.html";
} else {
    // 从配置获取API站点
    $site = getApiSite($source, $API_SITES);
    if (!$site) {
        jsonResponse(['code' => 400, 'msg' => '未找到有效的数据源']);
    }
    
    $baseUrl = isset($site['detail']) ? $site['detail'] : str_replace('/index.php/vod/search.html', '', $site['url']);
    $detailUrl = "$baseUrl/index.php/vod/detail/id/$id.html";
}

// 发送请求获取详情页
$html = sendApiRequest($detailUrl);
if (!$html) {
    jsonResponse(['code' => 400, 'msg' => '获取详情失败，请稍后重试']);
}

// 根据不同源提取播放链接
$matches = [];
if ($source === 'ffzy') {
    preg_match_all('/(?<=\$)(https?:\/\/[^"\'\\s]+?\/\d{8}\/\d+_[a-f0-9]+\/index\.m3u8)/i', $html, $matches);
} else {
    preg_match_all('/\$(https?:\/\/[^"\'\\s]+?\.m3u8)/i', $html, $matches);
}

$episodes = isset($matches[1]) ? $matches[1] : [];

// 返回结果
jsonResponse([
    'episodes' => $episodes,
    'detailUrl' => $detailUrl
]); 