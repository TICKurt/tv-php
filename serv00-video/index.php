<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// 需要登录才能访问
requireLogin();

// 处理注销
if (isset($_GET['logout'])) {
    logoutUser();
    redirect('/login.php');
}
?>
<!DOCTYPE html>
<html lang="zh" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>视频搜索系统</title>
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
            --color-btn-primary: #6366f1;
            --color-btn-hover: #4f46e5;
            --color-gradient-from: #1e293b;
            --color-gradient-to: #64748b;
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
            --color-btn-primary: #6366f1;
            --color-btn-hover: #4f46e5;
            --color-gradient-from: #f9fafb;
            --color-gradient-to: #94a3b8;
        }

        .page-bg {
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 25px 25px, rgba(0, 0, 0, 0.05) 2%, transparent 0%),
                radial-gradient(circle at 75px 75px, rgba(0, 0, 0, 0.05) 2%, transparent 0%);
            background-size: 100px 100px;
        }
        
        .dark .page-bg {
            background-image: 
                radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.05) 2%, transparent 0%),
                radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.05) 2%, transparent 0%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
            border: 1px solid var(--color-card-border);
            backdrop-filter: blur(10px);
            background-color: var(--color-card-bg);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .card-hover:hover {
            border-color: var(--color-accent);
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-text {
            background: linear-gradient(to right, var(--color-gradient-from), var(--color-gradient-to));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .button-glow {
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .button-glow:after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        
        .button-glow:hover:after {
            opacity: 1;
        }
        
        .settings-panel {
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
            background-color: var(--color-card-bg);
            border-left: 1px solid var(--color-border);
        }
        
        .settings-panel.show {
            transform: translateX(0);
        }
        
        /* 自定义滚动条样式 */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--color-bg-secondary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-text-secondary);
        }
        
        /* Firefox 滚动条样式 */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--color-border) var(--color-bg-secondary);
        }
        
        .search-container {
            position: relative;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .search-input {
            transition: all 0.3s ease;
            border: 1px solid var(--color-border);
            background: var(--color-bg-secondary);
            color: var(--color-text-primary);
            backdrop-filter: blur(10px);
        }
        
        .search-input:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        
        .search-button {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            background-color: var(--color-btn-primary);
        }
        
        .search-button:hover {
            background-color: var(--color-btn-hover);
        }
        
        .modal-content {
            animation: modalFadeIn 0.3s ease forwards;
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-border);
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .episode-button {
            transition: all 0.2s ease;
            background-color: var(--color-bg-secondary);
            color: var(--color-text-primary);
            border: 1px solid var(--color-border);
        }
        
        .episode-button:hover {
            transform: translateY(-2px);
            background-color: var(--color-accent);
            color: white;
            border-color: var(--color-accent);
        }
        
        .theme-toggle {
            width: 50px;
            height: 26px;
            border-radius: 15px;
            background-color: var(--color-bg-secondary);
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: 1px solid var(--color-border);
        }
        
        .theme-toggle:before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            background-color: var(--color-accent);
            transition: transform 0.3s ease;
        }
        
        .dark .theme-toggle:before {
            transform: translateX(24px);
        }
    </style>
</head>
<body class="page-bg font-sans">
    <div class="fixed top-6 right-6 z-50 flex items-center space-x-4">
        <div id="themeToggle" class="theme-toggle flex items-center justify-between px-1.5" onclick="toggleTheme()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </div>
        <button onclick="toggleSettings(event)" class="bg-opacity-80 hover:bg-opacity-100 border rounded-xl px-4 py-2.5 transition-all button-glow" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
        <a href="?logout=1" class="bg-opacity-80 hover:bg-opacity-100 border rounded-xl px-4 py-2.5 transition-all button-glow" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </a>
    </div>
    
    <!-- 设置面板 -->
    <div id="settingsPanel" class="settings-panel fixed right-0 top-0 h-full w-80 p-6 z-40">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-bold gradient-text">设置</h3>
            <button onclick="toggleSettings()" class="h-8 w-8 flex items-center justify-center rounded-full transition-colors" style="color: var(--color-text-secondary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-2" style="color: var(--color-text-secondary);">选择采集站点</label>
                <select id="apiSource" class="w-full px-3 py-2.5 rounded-lg focus:outline-none focus:border-indigo-500 transition-colors" style="background-color: var(--color-bg-secondary); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                <?php foreach ($API_SITES as $key => $site): ?>
                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($site['remark']); ?></option>
                <?php endforeach; ?>
                    <option value="custom">自定义接口</option>
                </select>
            </div>
            
            <!-- 添加自定义接口输入框 -->
            <div id="customApiInput" class="hidden">
                <label class="block text-sm font-medium mb-2" style="color: var(--color-text-secondary);">自定义接口地址</label>
                <input 
                    type="text" 
                    id="customApiUrl" 
                    class="w-full px-3 py-2.5 rounded-lg focus:outline-none transition-colors"
                    placeholder="请输入接口地址..."
                    style="background-color: var(--color-bg-secondary); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                >
            </div>
            
            <div class="mt-6 pt-4" style="border-top: 1px solid var(--color-border);">
                <p class="text-xs" style="color: var(--color-text-secondary);">当前站点代码：
                    <span id="currentCode" style="color: var(--color-text-primary); font-family: monospace;"></span>
                    <span id="siteStatus" class="ml-2"></span>
                </p>
            </div>

            <div class="mt-6 pt-4" style="border-top: 1px solid var(--color-border);">
                <p class="text-xs" style="color: var(--color-text-secondary);">
                    当前用户: <span style="color: var(--color-text-primary);"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </p>
                <a href="?logout=1" class="text-xs text-indigo-500 hover:text-indigo-400 mt-2 inline-block">退出登录</a>
            </div>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-8 flex flex-col h-screen">
        <div class="flex-1 flex flex-col">
            <!-- 搜索区域：默认居中 -->
            <div id="searchArea" class="flex-1 flex flex-col items-center justify-center transition-all duration-500 ease-out">
                <h1 class="text-5xl sm:text-6xl font-bold gradient-text mb-16">视频搜索</h1>
                <div class="w-full max-w-2xl">
                    <div class="flex search-container">
                        <input type="text" 
                               id="searchInput" 
                               class="w-full search-input px-6 py-4 rounded-l-xl focus:outline-none" 
                               placeholder="搜索你喜欢的视频...">
                        <button onclick="search()" 
                                class="px-8 py-4 text-white font-medium rounded-r-xl transition-colors search-button">
                            搜索
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- 搜索结果：初始隐藏 -->
            <div id="resultsArea" class="w-full hidden mt-10">
                <div id="results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                </div>
            </div>
        </div>
    </div>
    
    <!-- 详情模态框 -->
    <div id="modal" class="fixed inset-0 hidden z-50 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);">
        <div class="modal-content p-6 sm:p-8 rounded-xl w-11/12 max-w-4xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-6 flex-none">
                <h2 id="modalTitle" class="text-2xl font-bold gradient-text"></h2>
                <button onclick="closeModal()" class="h-10 w-10 flex items-center justify-center rounded-full transition-colors" style="color: var(--color-text-secondary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="overflow-auto flex-1 min-h-0">
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                </div>
            </div>
        </div>
    </div>
    
    <!-- 错误提示框 -->
    <div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 opacity-0 -translate-y-full">
        <p id="toastMessage"></p>
    </div>
    
    <!-- 添加 loading 提示框 -->
    <div id="loading" class="fixed inset-0 hidden items-center justify-center z-50" style="backdrop-filter: blur(8px);">
        <div class="p-6 rounded-xl flex items-center space-x-4" style="background-color: var(--color-card-bg); border: 1px solid var(--color-border);">
            <div class="w-7 h-7 rounded-full animate-spin" style="border: 3px solid var(--color-accent); border-top-color: transparent;"></div>
            <p style="color: var(--color-text-primary);" class="text-lg">加载中...</p>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html> 