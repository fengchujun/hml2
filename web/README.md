# HuaMuLan Tea - PC Web English/Chinese Version

PC端网站双语版本（中文/英文）

## 目录结构

```
web/
├── index.php           # 首页
├── products.php        # 产品列表
├── product.php         # 产品详情
├── news.php            # 新闻列表
├── article.php         # 文章详情
├── about.php           # 关于我们
├── contact.php         # 联系我们
├── search.php          # 搜索页面
├── config.php          # 配置文件（数据库等）
├── init.php            # 初始化文件
├── includes/
│   ├── header.php      # 中文头部
│   ├── header_en.php   # 英文头部
│   ├── footer.php      # 中文底部
│   ├── footer_en.php   # 英文底部
│   └── template.php    # 模板辅助函数
├── lang/
│   ├── zh.php          # 中文语言包
│   └── en.php          # 英文语言包
├── assets/
│   ├── css/
│   │   └── style.css   # 主样式表
│   ├── js/
│   │   └── main.js     # 主脚本
│   └── images/         # 图片资源
└── sql/
    └── add_english_fields.sql  # 数据库更新SQL
```

## 安装步骤

### 1. 数据库更新

执行 `sql/add_english_fields.sql` 添加英文字段：

```sql
-- 在MySQL中执行
source /path/to/web/sql/add_english_fields.sql
```

或者手动执行：

```sql
ALTER TABLE `hml_goods_category`
ADD COLUMN `category_name_en` varchar(50) NOT NULL DEFAULT '' COMMENT 'Category name (English)' AFTER `category_name`;

ALTER TABLE `hml_goods`
ADD COLUMN `goods_name_en` varchar(255) NOT NULL DEFAULT '' COMMENT 'Goods name (English)' AFTER `goods_name`,
ADD COLUMN `goods_content_en` text COMMENT 'Goods content (English)' AFTER `goods_content`;

ALTER TABLE `hml_article`
ADD COLUMN `article_title_en` varchar(255) NOT NULL DEFAULT '' COMMENT 'Article title (English)' AFTER `article_title`,
ADD COLUMN `article_content_en` text COMMENT 'Article content (English)' AFTER `article_content`;
```

### 2. 配置数据库连接

编辑 `config.php`，修改数据库配置：

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hml3');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. 添加图片资源

将以下图片放入 `assets/images/` 目录：
- `logo.png` - 网站logo
- `logo-white.png` - 白色版logo（用于底部）
- `banner.jpg` - 首页横幅图
- `no-image.jpg` - 缺省图片

## 使用说明

### URL 格式

- 中文版：`/web/index.php?lang=zh`
- 英文版：`/web/index.php?lang=en`

### 语言切换

1. **自动检测**：首次访问时，系统会检测浏览器语言，英文浏览器自动进入英文版
2. **手动切换**：点击右上角语言切换按钮
3. **Cookie记忆**：用户的语言选择会保存到Cookie中

### 数据填充

在后台管理系统中，为商品、分类、文章填写对应的英文内容：

- `hml_goods_category.category_name_en` - 分类英文名
- `hml_goods.goods_name_en` - 商品英文名
- `hml_goods.goods_content_en` - 商品英文详情
- `hml_article.article_title_en` - 文章英文标题
- `hml_article.article_content_en` - 文章英文内容

如果英文字段为空，系统会自动显示中文内容作为fallback。

## 后台管理

建议在现有后台添加英文字段的编辑功能，或者直接在数据库中更新英文内容。

## 注意事项

1. 确保服务器支持PHP 7.0+
2. 需要PDO MySQL扩展
3. 图片资源需要自行添加
4. 建议配置伪静态规则美化URL
