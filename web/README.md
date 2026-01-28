# 茶祖源·木兰阁 PC网页版

## 项目介绍

这是茶祖源·木兰阁的PC网页展示系统,用于展示茶叶产品、会客厅信息和企业理念。网站只做展示用,不包含用户交互功能。当用户点击购买或预约按钮时,会弹出小程序二维码,引导用户扫码进入小程序完成交互操作。

## 项目结构

```
web/
├── config.php              # 配置文件(API地址、文章ID等)
├── api.php                 # API调用函数封装
├── index.php               # 首页
├── concept-detail.php      # 核心理念详情页
├── teahouse-detail.php     # 会客厅详情页
├── products.php            # 产品列表页
├── product-detail.php      # 产品详情页
├── templates/              # 公共模板目录
│   ├── header.php          # 公共头部
│   ├── footer.php          # 公共底部
│   └── styles.php          # 公共样式
└── README.md               # 项目说明文档
```

## 功能说明

### 1. 首页 (index.php)
- 展示英雄视频背景
- 核心理念展示(4个卡片)
- 会客厅展示(3个)
- 产品中心展示(从API获取产品分类)

### 2. 核心理念详情页 (concept-detail.php)
- 展示企业理念、品质观念、产品手册、品牌介绍四个文章
- 通过参数`?id=文章ID`访问不同文章
- 左侧边栏导航切换
- 默认显示文章ID为1的企业理念

### 3. 会客厅详情页 (teahouse-detail.php)
- 展示所有会客厅信息
- 从API获取会客厅列表
- 显示会客厅图片、介绍、联系方式等
- 支持预约功能(扫码)

### 4. 产品列表页 (products.php)
- 展示所有产品分类
- 从API获取产品分类列表
- 左侧边栏分类导航
- 点击产品卡片弹出二维码

### 5. 产品详情页 (product-detail.php)
- 展示单个产品的详细信息
- 通过参数`?id=产品ID`访问不同产品
- 显示产品图片、价格、规格、库存等
- 点击购买按钮弹出二维码

## API接口

本项目使用以下API接口:

1. **产品分类**: `POST /api/goodscategory/tree`
2. **产品详情**: `POST /api/goodssku/detail`
3. **会客厅列表**: `POST /notes/api/notes/lists`
4. **文章列表**: `POST /api/article/page`
5. **文章详情**: `POST /api/article/info`

## 配置说明

在`config.php`文件中可以配置以下内容:

```php
// API配置
define('API_BASE_URL', 'https://test.ayatimes.com');
define('STORE_ID', 2);

// 核心理念文章ID配置
define('ARTICLE_ID_CONCEPT_1', 1); // 企业理念
define('ARTICLE_ID_CONCEPT_2', 2); // 品质观念
define('ARTICLE_ID_CONCEPT_3', 3); // 产品手册
define('ARTICLE_ID_CONCEPT_4', 4); // 品牌介绍
define('ARTICLE_ID_CONTACT', 6);   // 联系我们

// 小程序二维码
define('MINIPROGRAM_QR_CODE', '二维码图片URL');
```

## 安装部署

### 环境要求
- PHP 7.0 或更高版本
- 支持cURL扩展

### 部署步骤

1. 将`web`目录上传到服务器
2. 配置Web服务器(Apache/Nginx)将网站根目录指向`web`目录
3. 确保PHP有权限读取所有文件
4. 访问 `http://你的域名/index.php` 查看首页

### Nginx配置示例

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/hml/web;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Apache配置示例

在`web`目录下创建`.htaccess`文件:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

## 页面访问

- 首页: `index.php`
- 核心理念: `concept-detail.php?id=1` (1,2,3,4对应不同文章)
- 会客厅: `teahouse-detail.php`
- 产品列表: `products.php`
- 产品详情: `product-detail.php?id=3` (3为产品ID)

## 二维码弹窗

所有需要用户交互的地方(购买、预约、咨询等)都会弹出小程序二维码,引导用户扫码进入小程序操作。

二维码配置在`config.php`文件中:
```php
define('MINIPROGRAM_QR_CODE', '小程序二维码图片URL');
```

## 注意事项

1. 本系统纯展示,不包含任何后台管理功能
2. 所有内容通过API动态获取
3. 修改文章ID需要在`config.php`中配置
4. 确保服务器能够访问API地址
5. 如果API返回数据为空,页面会显示默认提示信息

## 技术栈

- PHP 7.0+
- HTML5
- CSS3
- JavaScript (原生)
- cURL (API请求)

## 联系方式

如有问题,请通过小程序联系我们。
