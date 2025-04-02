// 全局变量
let currentApiSource = localStorage.getItem('currentApiSource') || 'hm';
let customApiUrl = localStorage.getItem('customApiUrl') || '';

// 初始化
document.addEventListener('DOMContentLoaded', function() {
    // 设置API源选择器的默认值
    document.getElementById('apiSource').value = currentApiSource;
    
    // 如果是自定义API，显示输入框
    if (currentApiSource === 'custom') {
        document.getElementById('customApiInput').classList.remove('hidden');
        document.getElementById('customApiUrl').value = customApiUrl;
    }
    
    // 更新当前站点状态
    updateCurrentSiteStatus();
    
    // 监听API源变化
    document.getElementById('apiSource').addEventListener('change', function(e) {
        currentApiSource = e.target.value;
        localStorage.setItem('currentApiSource', currentApiSource);
        
        if (currentApiSource === 'custom') {
            document.getElementById('customApiInput').classList.remove('hidden');
        } else {
            document.getElementById('customApiInput').classList.add('hidden');
        }
        
        updateCurrentSiteStatus();
    });
    
    // 监听自定义API输入
    document.getElementById('customApiUrl').addEventListener('change', function(e) {
        customApiUrl = e.target.value;
        localStorage.setItem('customApiUrl', customApiUrl);
        updateCurrentSiteStatus();
    });
    
    // 监听回车搜索
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            search();
        }
    });
});

// 更新当前站点状态
async function updateCurrentSiteStatus() {
    const currentCode = document.getElementById('currentCode');
    currentCode.textContent = currentApiSource;
    
    const isAvailable = await testSiteAvailability(currentApiSource);
    updateSiteStatus(isAvailable);
}

// 测试站点可用性
async function testSiteAvailability(source) {
    try {
        const apiParams = source === 'custom' 
            ? '&customApi=' + encodeURIComponent(customApiUrl)
            : '&source=' + source;
            
        const response = await fetch('/api/search.php?wd=test' + apiParams);
        const data = await response.json();
        return data.code !== 400;
    } catch (error) {
        return false;
    }
}

// 更新站点状态显示
function updateSiteStatus(isAvailable) {
    const statusEl = document.getElementById('siteStatus');
    if (isAvailable) {
        statusEl.innerHTML = '<span style="color: #10b981;">●</span> <span style="color: #34d399;">可用</span>';
    } else {
        statusEl.innerHTML = '<span style="color: #ef4444;">●</span> <span style="color: #f87171;">不可用</span>';
    }
}

// 显示/隐藏设置面板
function toggleSettings(e) {
    e && e.stopPropagation();
    const panel = document.getElementById('settingsPanel');
    panel.classList.toggle('show');
}

// 显示提示消息
function showToast(message, type = 'error') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    const bgColors = {
        'error': 'bg-red-500',
        'success': 'bg-emerald-500',
        'info': 'bg-blue-500'
    };
    
    const bgColor = bgColors[type] || bgColors.error;
    toast.className = `fixed top-6 left-1/2 -translate-x-1/2 px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 ${bgColor} text-white`;
    toastMessage.textContent = message;
    
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(-100%)';
    }, 3000);
}

// 显示/隐藏加载动画
function showLoading() {
    document.getElementById('loading').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loading').style.display = 'none';
}

// 搜索视频
async function search() {
    const query = document.getElementById('searchInput').value.trim();
    if (!query) {
        showToast('请输入搜索内容');
        return;
    }
    
    showLoading();
    
    try {
        const apiParams = currentApiSource === 'custom' 
            ? '&customApi=' + encodeURIComponent(customApiUrl)
            : '&source=' + currentApiSource;
        
        const response = await fetch('/api/search.php?wd=' + encodeURIComponent(query) + apiParams);
        const data = await response.json();
        
        if (data.code === 400) {
            showToast(data.msg);
            return;
        }
        
        // 调整搜索框位置
        document.getElementById('searchArea').classList.remove('flex-1');
        document.getElementById('searchArea').classList.add('mb-8');
        document.getElementById('resultsArea').classList.remove('hidden');
        
        // 显示搜索结果
        const resultsDiv = document.getElementById('results');
        if (data.list && data.list.length > 0) {
            resultsDiv.innerHTML = data.list.map(item => `
                <div class="card-hover rounded-xl overflow-hidden cursor-pointer p-6 h-fit transition-all" 
                     onclick="showDetails('${item.vod_id}','${item.vod_name}')">
                    <h3 class="text-xl font-semibold mb-3" style="color: var(--color-text-primary);">
                        ${item.vod_name}
                    </h3>
                    <p style="color: var(--color-text-secondary);" class="text-sm mb-2">
                        ${item.type_name || '未知类型'}
                    </p>
                    <p class="text-sm font-medium" style="color: var(--color-accent);">
                        ${item.vod_remarks || '更新中'}
                    </p>
                </div>
            `).join('');
        } else {
            resultsDiv.innerHTML = `
                <div class="col-span-full text-center py-10" style="color: var(--color-text-secondary);">
                    没有找到相关视频
                </div>
            `;
        }
    } catch (error) {
        showToast('搜索请求失败，请稍后重试');
    } finally {
        hideLoading();
    }
}

// 显示视频详情
async function showDetails(id, title) {
    showLoading();
    
    try {
        const apiParams = currentApiSource === 'custom' 
            ? '&customApi=' + encodeURIComponent(customApiUrl)
            : '&source=' + currentApiSource;
            
        const response = await fetch('/api/detail.php?id=' + id + apiParams);
        const data = await response.json();
        
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        modalTitle.textContent = title;
        
        if (data.episodes && data.episodes.length > 0) {
            modalContent.innerHTML = `
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                    ${data.episodes.map((url, index) => `
                        <button onclick="playVideo('${url}','${title}',${index+1})" 
                                class="episode-button px-4 py-2.5 rounded-lg transition-all text-center">
                            第${index + 1}集
                        </button>
                    `).join('')}
                </div>
            `;
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-10" style="color: var(--color-text-secondary);">
                    暂无可播放的视频源
                </div>
            `;
        }
        
        modal.classList.remove('hidden');
    } catch (error) {
        showToast('获取详情失败，请稍后重试');
    } finally {
        hideLoading();
    }
}

// 关闭模态框
function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}

// 播放视频
function playVideo(url, title, episode) {
    const encodedUrl = encodeURIComponent(url);
    const encodedTitle = encodeURIComponent(title);
    window.open(`/player.php?url=${encodedUrl}&title=${encodedTitle}&episode=${episode}`, '_blank');
}

// 点击外部关闭设置面板
document.addEventListener('click', function(e) {
    const panel = document.getElementById('settingsPanel');
    const settingsButton = document.querySelector('button[onclick="toggleSettings(event)"]');
    
    if (!panel.contains(e.target) && !settingsButton.contains(e.target) && panel.classList.contains('show')) {
        panel.classList.remove('show');
    }
}); 