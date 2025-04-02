<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// 已登录则跳转至首页
if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';

// 处理登录表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = '用户名和密码不能为空';
    } else if (authenticateUser($username, $password)) {
        redirect('/index.php');
    } else {
        $error = '用户名或密码不正确';
    }
}
?>
<!DOCTYPE html>
<html lang="zh" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 视频搜索系统</title>
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
        
        .login-card {
            backdrop-filter: blur(10px);
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-card-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .gradient-text {
            background: linear-gradient(to right, #1e293b, #64748b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .dark .gradient-text {
            background: linear-gradient(to right, #f9fafb, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="page-bg">
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="w-full max-w-md p-8 login-card rounded-xl">
            <h1 class="text-3xl font-bold mb-6 text-center gradient-text">视频搜索系统</h1>
            <h2 class="text-xl mb-8 text-center" style="color: var(--color-text-secondary);">用户登录</h2>
            
            <?php if ($error): ?>
                <div class="bg-red-500 text-white p-3 rounded-lg mb-6 text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium mb-2" style="color: var(--color-text-secondary);">用户名</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                        style="background-color: var(--color-bg-secondary); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                        required
                    >
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium mb-2" style="color: var(--color-text-secondary);">密码</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                        style="background-color: var(--color-bg-secondary); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                        required
                    >
                </div>
                <div>
                    <button 
                        type="submit" 
                        class="w-full px-4 py-3 text-white font-medium rounded-lg transition-colors hover:bg-indigo-600"
                        style="background-color: var(--color-accent);"
                    >
                        登录
                    </button>
                </div>
                <div class="text-center text-sm mt-4" style="color: var(--color-text-secondary);">
                    默认用户名: movie<br>
                    默认密码: movie
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // 检查并设置主题
        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        }
        
        // 初始化主题
        initTheme();
    </script>
</body>
</html> 