<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// 需要登录才能访问
requireLogin();

$url = $_GET['url'] ?? '';
$title = $_GET['title'] ?? '未知视频';
$episode = $_GET['episode'] ?? 1;

if (empty($url)) {
    die('播放地址不能为空');
}
?>
<!DOCTYPE html>
<html lang="zh" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - 第<?php echo htmlspecialchars($episode); ?>集</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    <style>
        :root {
            --color-bg-primary: #f8fafc;
            --color-bg-secondary: #f1f5f9;
            --color-text-primary: #0f172a;
            --color-text-secondary: #334155;
            --color-accent: #6366f1;
            --color-border: #e2e8f0;
            --color-card-bg: #ffffff;
            --color-card-border: #e2e8f0;
        }
        
        .dark {
            --color-bg-primary: #0f172a;
            --color-bg-secondary: #1e293b;
            --color-text-primary: #f8fafc;
            --color-text-secondary: #cbd5e1;
            --color-accent: #6366f1;
            --color-border: #334155;
            --color-card-bg: #1e293b;
            --color-card-border: #334155;
        }

        .page-bg {
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
            min-height: 100vh;
        }
        
        .gradient-text {
            background: linear-gradient(to right, var(--color-gradient-from), var(--color-gradient-to));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="page-bg">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold mb-2" style="color: var(--color-text-primary);">
                <?php echo htmlspecialchars($title); ?>
            </h1>
            <p class="text-lg" style="color: var(--color-text-secondary);">
                第<?php echo htmlspecialchars($episode); ?>集
            </p>
        </div>
        
        <div class="aspect-video rounded-xl overflow-hidden shadow-xl bg-black">
            <iframe 
                src="https://hoplayer.com/index.html?url=<?php echo htmlspecialchars($url); ?>&autoplay=true"
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="no" 
                allowfullscreen="true">
            </iframe>
        </div>
        
        <div class="mt-6 text-center">
            <a href="/" class="inline-block px-6 py-3 rounded-lg transition-colors" 
               style="background-color: var(--color-accent); color: white;">
                返回搜索
            </a>
        </div>
    </div>
    
    <script>
        // 检查并设置主题
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html> 