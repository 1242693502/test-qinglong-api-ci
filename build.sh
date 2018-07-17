#!/bin/bash

build(){
  echo 'Start Build:'

  case $1 in
    dev)
        #研发环境
        build_dev
        ;;
    test)
        #测试环境
        build_dev
        ;;
    pre)
        #预发布环境
        build_prod
        ;;
    prod)
        #生产环境
        build_prod
        ;;
    *)
        echo "Usage: sh $0 build {dev|test|pre|prod}"
    esac
}

build_dev(){
  echo 'DEV'

  #检查是否安装 composer
  if ! command -v composer >/dev/null 2>&1; then
        echo '命令 composer 不存在, 请安装后执行本脚本'
        exit 7
  fi

  #composer 安装PHP依赖
  composer --dev --no-progress install

}

build_prod(){
  echo 'PROD'

  #检查是否安装 composer
  if ! command -v composer >/dev/null 2>&1; then
        echo '命令 composer 不存在, 请安装后执行本脚本'
        exit 7
  fi

  #composer 安装PHP依赖
  composer --no-dev --optimize-autoloader --no-progress install
}

getconfig(){
  config_files='.env'
  echo $config_files
}

createdir(){
  echo '创建相关目录'
}

setpermission(){

  SHELL_FOLDER=$(cd "$(dirname "$0")";pwd)
  cd $SHELL_FOLDER

  writable_dir=('storage/' 'bootstrap/cache/')
  for dir in ${writable_dir[@]};do
    mkdir -p $dir
    chown -R $1 $dir
  done
}

case $1 in
    build)
        build $2
        ;;
    getconfig)
        getconfig
        ;;
    setpermission)
        #传递一个用户进去，修改相应权限
        setpermission $2
        ;;
    *)
        echo "Usage: sh $0 {build|getconfig|setpermission}"
esac
