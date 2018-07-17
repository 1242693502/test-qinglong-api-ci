# 系统部署说明

## 环境需求

### 必须
- composer >= 1.0.0
- PHP >= 7.1
- MySQL = 5.6.0
- nginx >= 1.6.0
- php-fpm

### 可选
- redis >= 2.8

## 安装步骤

### 配置环境变量

复制 `.env.example` 到 `.env` ，修改环境变量


### 后续处理

1. 项目目录执行 `composer install` 更新依赖库
2. 项目目录执行 `php artisan key:generate` 生成加密key

## 队列与定时任务部署

### 队列部署

#### 安装setuptools 

     wget https://bootstrap.pypa.io/ez_setup.py -O - | sudo python
    
#### 安装supervisor

     sudo easy_install supervisor

##### 配置supervisor

     请自己参考 supervisor 文档
    
### 定时任务部署

添加下列命令到 `crontab -e` 中，注意用户权限的问题

    * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1

##更新sass

gulp

## 后续处理

访问页面