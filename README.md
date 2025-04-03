# TV-PHP

🎬 基于 PHP 开发的视频解析站点，专用于 serv00 环境部署的增强版本。本项目是 [bestK/tv](https://github.com/bestK/tv) 的 PHP 重构版本，增加了用户认证等功能。

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://php.net/)
[![License](https://img.shields.io/github/license/your-username/tv-php)](LICENSE)

## ✨ 功能特点

- 🔒 用户认证系统
  - 安全的登录/注册功能
  - 会话管理
  - 用户权限控制
- 🎯 视频解析核心功能
  - 支持多个主流视频平台
  - 高效的视频地址解析
  - 稳定的播放体验
- 🛠️ 专为 serv00 环境优化
  - 性能优化
  - 环境适配
  - 部署便捷

## 🚀 快速开始

### 系统要求

- PHP >= 7.4
- serv00 环境
- Node.js & npm

### 安装步骤

> ⚠️ 案例路径为 /home/your_name/tv-php/serv00-video 注意根据实际情况替换！

1. 克隆仓库：

```sh
git clone https://github.com/your-username/tv-php.git
cd tv-php/serv00-video/
```

2. 设置权限：

```sh
chmod -R 755 .
chmod -R 777 storage/
```

3. 安装和配置 PM2：

a. 安装 PM2：

```sh
# 检查 npm 全局安装路径
npm config get prefix

# 一键脚本安装 PM2
bash <(curl -s https://raw.githubusercontent.com/k0baya/alist_repl/main/serv00/install-pm2.sh)

# 安装 PM2（确保使用正确的用户名，替换your_name）
npm install -g pm2 --prefix=/home/your_name/.npm-global

# 创建 .bashrc（如果不存在）
touch ~/.bashrc

# 编辑 .bashrc 添加 PATH（确保使用正确的用户名，替换your_name）
echo 'export PATH="$PATH:/home/your_name/.npm-global/bin"' >> ~/.bashrc

# 加载新配置
source ~/.bashrc

# 验证安装
pm2 --version
```

b. 使用 PM2 启动 PHP 内置服务器：

```sh
# 检查 PHP 环境
php -v

# 使用 PM2 启动 PHP 内置服务器（PHP >= 5.4），（注意匹配端口还有your_name）
pm2 start php --name "php-server" -- -S 0.0.0.0:8000 -t /home/your_name/tv-php/serv00-video

参数说明：
- `--name "php-server"`: 为进程指定名称
- `-S 0.0.0.0:8000`: 监听所有网络接口的 8000 端口
- `-t /home/your_name/code/serv00-video`: 指定项目根目录路径

```

c. 其他pm2命令

```sh
# 查看运行状态
pm2 list

# 查看日志
pm2 logs

# 停止服务
pm2 stop php-server

# 重启服务
pm2 restart php-server
```

⚠️ 注意事项：

1. 确保 PHP 版本 >= 5.4
2. 如遇端口冲突，可修改端口号（如 8080）
3. 请根据实际情况修改项目路径

## 📚 目录结构

```
tv-php/
├── api/          # API 接口文件
├── configs/      # 配置文件
├── includes/     # 核心类和函数
├── js/          # JavaScript 文件
├── login.php    # 登录处理
├── player.php   # 播放器页面
└── index.php    # 入口文件
```

## 🔧 配置说明

主要配置文件位于 `configs/config.php`，包含以下配置项：

- API 接口配置

## 📄 开源协议

本项目基于 MIT 协议开源 - 查看 [LICENSE](LICENSE) 文件了解更多信息

## 🙏 致谢

- [bestK/tv](https://github.com/bestK/tv) - 原始项目

## 📞 联系方式

如有问题或建议，欢迎提交 [Issue](https://github.com/TICKurt/tv-php/issues)
