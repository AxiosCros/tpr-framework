# tpr-framework 
专为接口开发而设计

> 本版本为基于thinkphp5.0.9的修订版(thinkphp的restFulApi版)

> 新增或修改了一些功能和特性，比如请求参数的接口验证，前置后置中间件，异步任务等等

> thinkphp5 github地址 [https://github.com/top-think/framework](https://github.com/top-think/framework)

> 安装方式  :   composer require axios/tpr-framework

> 交流QQ群：521797692

## 使用案例

> tpr-cms 最新的master分支 [https://github.com/AxiosCros/tpr-cms](https://github.com/AxiosCros/tpr-cms)

## 修改记录简介
* 修改一些宏定义变量的默认值
 > 即修改了默认的项目目录结构，将配置目录与应用目录分离
 ``` php
 ├─application           应用目录
 │  ├─common             公共模块目录（可以更改）
 │  └─module_name        模块目录
 │      ├─config.php      模块配置文件
 │      ├─common.php      模块函数文件
 │      ├─controller      控制器目录
 │      ├─model           模型目录
 │      ├─view            视图目录
 │      └─ ...            更多类库目录
 │  
 ├─config                配置目录 
 │  ├─command.php        命令行工具配置文件
 │  ├─common.php         公共函数文件
 │  ├─config.php         公共配置文件
 │  ├─route.php          路由配置文件
 │  ├─tags.php           应用行为扩展定义文件
 │  └─database.php       数据库配置文件
 │
 ├─public                WEB目录（对外访问目录）
 │  ├─index.php          入口文件
 │  ├─router.php         快速测试文件
 │  └─.htaccess          用于apache的重写
 │
 ├─extend                扩展类库目录
 ├─runtime               应用的运行时目录（可写，可定制）
 ├─vendor                第三方类库目录（Composer依赖库）
 ├─composer.json         composer 定义文件
 ├─LICENSE.txt           授权说明文件
 ├─README.md             README 文件
 └─think                 命令行入口文件
 ```
* 系统语言包加载 增加 CONF_PATH/lang/ 目录

* traits\controller\Jump 增加wrong和response方法用于接口数据回调
  ``` php
  支持数据转义,如整型转为字符串,null转为空字符串,对象转为数组等
  ```
 
* 实现自定义回调格式
  ``` shell
  默认回调格式为json,
  但是某个接口需要回调xml时，
  只需在回调前使用 $this->return_type = 'xml'即可
  ```

* Env 类增加getFromFile方法、config方法、set方法和save方法
  * getFromFile方法实现环境变量支持从文件读取，支持一级/三级变量获取
  * config方法实现自定义设置环境变量文件的位置
  * set方法实现环境变量的临时修改
  * save方法实现环境变量的持久化保存至某个环节变量文件

* 增加助手函数env()

* 增加助手函数c()方法获取带默认值的配置
  ``` php
  c($index,$default);
  ```

* 增加助手函数u(),方便url生成
  > u($module = 'index'', $controller = 'index'', $action = 'index'')
  
* 增加D()方法
 ``` php
 D($table_name,$config);
 ```
 
* Validate类的error参数默认值修改为空字符串

* 增加异步任务类Fork

* 增加类文件注释解析类 Doc

* 日志存储增加Mongo驱动

* Db类的connect方法支持通过传入配置名载入配置
  ``` php
  Db::connect('mysql.test')->name('test')->select();
  ```

* 增加AppInit应用初始化行为
  * 增加中间件功能
  * 请求参数自动验证功能
  * 增加请求缓存功能
 
* 增加请求结束行为监听
  > start.php
  ``` php
  Hook::listen('request_end');
  ```
  
* think/Controller增加$this->param参数

* Tool增加生成唯一标识的工具函数uuid()

* validate获取error信息经过lang()翻译后回调

* 增加加载APP_PATH目录下的配置文件
  > 修改后的配置优先级为 : 模块级别module > 应用级别app > 公共级别 CONF_PATH
  > 应用配置文件位于 APP_PATH/ 目录下
  > 公共配置文件位于 CONF_PATH/ 目录
  > 模块配置文件位于 CONF_PATH/module_name/ 目录下
  > 文件包含 config.php , database.php  , tags.php , extra/下的扩展配置 
  
* 增加RSA加密类Crypt

``` php
//生成密钥  CONF_PATH/key/for_api/
Crypt::makeKey('for_api');     // 生成一对公私密钥 for_api 为该密钥的命名

// 加密  pri私钥加密  pub公钥加密
Crypt::rsa('for_api')->encrypt($test,'pri');

// 解密  pri私钥解密  pub公钥解密
Crypt::rsa('for_api')->decrypt($encrypt,'pub');

```

## 开源协议
  > 遵循Apache2开源协议发布，并提供免费使用
  