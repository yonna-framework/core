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

## 

##### Yonna是轻松的框架，减去一切繁琐的事，让你不再恼火
##### 她有很多自建包，core核心库默认已经包含(composer.json)

```json
{
    "yonna/foundation": "@dev",
    "yonna/response": "@dev",
    "yonna/exception": "@dev",
    "yonna/log": "@dev"
}
```

 * 其中foundation为您提供各种丰富的助手函数类
 * 而response为您解决响应的痛点
 * exception方便舒适的处理错误抛出
 * log为你解决各种日志处理

## 

###下面的内容基于 yonna/yonna [(https://github.com/yonna-framework/yonna)](https://github.com/yonna-framework/yonna)
##### 参考 yonna/yonna 项目开始您的项目
##### 为您的项目在 public 创建一个 **index.php** 文件
##### 并使用Yonna进行boot，会返回一个 ResponseCollector 对象（yonna/response），您可以对它进行 json/xml/html/text 等格式化输出

 * 下面是一个例子：
```php
<?php
/**
 * 加载 composer 模块
 * 如果报错请安装 composer 并执行 composer install 安装所需要的包库
 *
 * Load composer modules
 * Install composer and exec composer install in your shell when error throw.
 */

require __DIR__ . '/vendor/autoload.php';

$ResponseCollector = Yonna\Core::bootstrap(
    realpath(__DIR__ . '/../'),
    'develop', // 设定为开发环境
    Yonna\Bootstrap\BootType::AJAX_HTTP
);

/**
 * end response
 */
$ResponseCollector->end();
?>
```


##

### Env 机制
```
上面的bootstrap使用了develop作为一个env的配置参数
yonna会自动根据你的配置
```


##

### Config 自动加载机制
```
你会发现在yonna/yonna项目中，只要在config使用了对应的配置，都可以立即使用，
这是因为Core核心会对app/config（不一定是app，这取决你bootstrap的root）下的实施自动加载
所以你可以在config里编写你的自定义配置
* 温馨提示，yonna/* 其他包，大部分也是基于这样的机制
```

##

### 核心自带一个 Exec 工具
```bash
在终端内轻击 
#:php exec
会进入yonna的命令行界面：（输入help，可以看到最新版支持的指令）
>Yonna<: help
 ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 ┃ Command List:
 ┃
 ┃     <cls | clear> 
 ┃           clean screen
 ┃     <ls | dir> 
 ┃           explore dir
 ┃     <die | exit> 
 ┃           exit exec
 ┃     <-h | help> 
 ┃           get command list
 ┃     <swh> <options: -p [PORT] -e [ENV]>
 ┃           start a swoole http server
 ┃     <swws> <options: -p [PORT] -e [ENV]>
 ┃           start a swoole websocket server
 ┃     <swt> <options: -p [PORT] -e [ENV]>
 ┃           start a swoole tcp server
 ┃     <pkg> <options: -c [CONFIG PATH]>
 ┃           package project
 ┃
 ┃     (count:8)
 ┃ Hope help you ~
 ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 
 exec支持 -o 参数实现连贯的指令调用
 此功能非常适合用于swoole服务器的开启，如：
 # php exec -o swws -p 2019 -e develop
                      [port]   [env]
 这样就不需要进入命令台，直接开启了一个websocket的swoole服务器！
```





