# YafUse - I Use Yaf!
Yaf 地址:https://github.com/laruence/php-yaf 请针对自己的php 版本进行安装,注意服务器url路由规则

## Why I Do
自己在项目中用了yaf，用了很多外部类，感觉需要优化地方很多，逐渐改进。这里只是一个简单的例子，可以用于yaf快速上手，我的修改有些借鉴别人的思路，想要用好希望多看鸟哥的官方文档及源码.(http://www.laruence.com/manual/)

## What I Do
- layout布局实现
- bootstrap 后台管理界面
- PDO数据库操作类(Mysql数据主从实现)
- 简单的增删改查实现
- 错误捕捉显示及日志记录

## Requirement
- Nginx
- PHP 5.2 +
- PHP Yet another Framework
- Mysql

## How To Use

### Rewrite rules

#### Apache

```conf
#.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* index.php
```

#### Nginx (nginx.conf已有示例)

```
server {
  listen ****;
  server_name  domain.com;
  root   document_root;
  index  index.php index.html index.htm;
 
  location / {
		try_files $uri $uri/ /index.php?$args;
  }

}
```
### Database
使用shidatabase.sql,简单的例子，只有一个数据表(增删改查实现)

### app.ini
详细见具体文件

### ErrorAction
报错开启关闭在ini中有配置，可以记录为日志文件，需要有写权限。在Error.php实现。
