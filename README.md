[![License](https://img.shields.io/github/license/yonna-framework/core.svg)](https://packagist.org/packages/yonna/core)
[![Repo Size](https://img.shields.io/github/repo-size/yonna-framework/core.svg)](https://packagist.org/packages/yonna/core)
[![Downloads](https://img.shields.io/packagist/dm/yonna/core.svg)](https://packagist.org/packages/yonna/core)
[![Version](https://img.shields.io/github/release/yonna-framework/core.svg)](https://packagist.org/packages/yonna/core)
[![Php](https://img.shields.io/packagist/php-v/yonna/core.svg)](https://packagist.org/packages/yonna/core)

## Yonna 核心库

```
Yonna是一个极其纯净的纯API-PHP框架.
轻松对接swoole，支持ajax/sw·http·websocket。
人性及强力的DB-ORM，摆脱Model编程
如连表自动前缀，类型自动转义/转换/解释
有力的Response令api数据轻松转换，支持json/xml/html等格式化输出
内置有趣的Exec方法，可实现服务、加密打包等方法
轻松开启swoole，又或是将你的业务php代码混淆到难以阅读的效果
```

## 

#### 如何安装

##### 可以通过composer安装：`composer require yonna/core`

##### 可以通过git下载：`git clone https://github.com/yonna-framework/core.git`

> 项目DEMO：[GOTO yonna](https://github.com/yonna-framework/yonna)

## 

### 在您的项目public 创建一个index.php
> 并使用Yonna进行boot，会返回一个 ResponseCollector 对象，您可以对它进行json/xml/html等格式化输出
```php
<?php
/**
 * 加载 composer 模块
 * 如果报错请安装 composer 并执行 composer install 安装所需要的包库
 *
 * Load composer modules
 * Install composer and exec composer install in your shell when error throw.
 */

require __DIR__ . '/../vendor/autoload.php';

$ResponseCollector = Yonna\Core::bootstrap(
    realpath(__DIR__ . '/../'),
    'example',
    Yonna\Mapping\BootType::AJAX_HTTP
);

/**
 * end response
 */
$ResponseCollector->end();
?>
```

### Exec

