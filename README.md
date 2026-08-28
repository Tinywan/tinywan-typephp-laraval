# TypePHP (TPC) Laravel 13 静态编译与原生二进制打包实践

本项目是基于 **TypePHP（TPC 编译器）** 将 **Laravel 13** 的核心运行机制、业务控制器以及 **Vendor 依赖组件（PSR、Monolog 3、Portable-ASCII、CssToInlineStyles 等 47 个核心源文件）** 一同编译为原生独立机器码二进制可执行程序的完整实战。

## 01 快速构建与运行

### 1. 纯代码编译 (Build)

```cmd
build.bat
```

> 调用 `tpc.exe project.yml`，通过 MSVC `cl.exe` + `link.exe` 批量编译 47 个 C++ 源码文件并链接生成原生二进制：`build/laravel_artisan.exe`。

### 2. 自动打包为独立分发包 (Package)

```cmd
package.bat
```

> 先执行 `build.bat`，随后将可执行文件、核心动态库 DLL、PHP 扩展（ext）、配置文件与框架运行时结构自动同步打包至 `dist/` 目录。

### 3. 运行测试 (Run)

```powershell
# 方式 1：进入 dist 目录执行启动脚本
cd dist
.\run.bat --version
.\run.bat route:list
.\run.bat about

# 方式 2：直接执行可执行文件
.\dist\laravel-artisan.exe list
```

## 02 编译包含的组件清单

在 [`project.yml`](file:///D:/dnmp/www/TypePHP/tinywan-typephp-laraval/project.yml) 中已将以下组件全部纳入静态 AOT 编译：

1. **框架 CLI 引导与核心调度**：`main.php`
2. **应用业务控制器**：`app/Http/Controllers/Controller.php`、`app/Http/Controllers/IndexController.php`
3. **PSR 系列核心契约规范**：
   - `vendor/psr/clock`
   - `vendor/psr/container`
   - `vendor/psr/event-dispatcher`
   - `vendor/psr/http-client`
   - `vendor/psr/http-factory`
   - `vendor/psr/http-message`
   - `vendor/psr/log`
   - `vendor/psr/simple-cache`
4. **Monolog 3 日志系统（40+ 核心类）**：
   - `Monolog\Level` (Enum)
   - `Monolog\Logger`、`Monolog\LogRecord`、`Monolog\ErrorHandler`、`Monolog\Registry`、`Monolog\Utils`
   - `Monolog\Formatter\*`（LineFormatter、JsonFormatter、NormalizerFormatter 等）
   - `Monolog\Handler\*`（StreamHandler、RotatingFileHandler、ErrorLogHandler、NullHandler 等）
   - `Monolog\Processor\*`（IntrospectionProcessor、MemoryUsageProcessor、UidProcessor、WebProcessor 等）
5. **工具库组件**：
   - `vendor/voku/portable-ascii/src/voku/helper/ASCII.php`
   - `vendor/tijsverkoyen/css-to-inline-styles/src`

## 03 业务控制器与接口案例实测

已编写并完成编译测试的控制器：[`app/Http/Controllers/IndexController.php`](file:///D:/dnmp/www/TypePHP/tinywan-typephp-laraval/app/Http/Controllers/IndexController.php)

### 1. 查看编译注入的路由列表
```powershell
cd D:\dnmp\www\TypePHP\tinywan-typephp-laraval\dist
.\run.bat route:list
```
**输出结果**：
```text
  GET|HEAD  / ................................................................................ IndexController@welcome
  GET|HEAD  index/benchmark ................................................................ IndexController@benchmark
  GET|HEAD  index/json .......................................................................... IndexController@json
  GET|HEAD  index/view ....................................................................... IndexController@welcome
  GET|HEAD  storage/{path} ................................................................. storage.local
  PUT       storage/{path} ................................................................. storage.local.upload
  GET|HEAD  up ............................................................................. Health Check
```

### 2. Web 接口 HTTP 访问测试
- **JSON 接口测试**：
  👉 `GET http://local.v13.laravel.tinywan.com/index/json`
  ```json
  {
    "code": 0,
    "msg": "ok",
    "data": {
      "engine": "TypePHP AOT Compiler",
      "framework": "Laravel 13",
      "timestamp": 1787885338,
      "datetime": "2026-08-28 02:48:58"
    }
  }
  ```
- **算法与基准测试接口**：
  👉 `GET http://local.v13.laravel.tinywan.com/index/benchmark`
  ```json
  {
    "code": 0,
    "msg": "benchmark finished",
    "data": {
      "iterations": 100000,
      "cost_ms": 2.422,
      "result": 7499925000
    }
  }
  ```
- **视图渲染接口**：
  👉 `GET http://local.v13.laravel.tinywan.com/index/view`（渲染 Laravel 13 欢迎首页）

## 04 踩坑与问题解决汇总 (Troubleshooting)

### 一、AOT 编译符号与类继承问题
* **现象**：`Fatal error: Class User inherits from a non-existent class Illuminate\Foundation\Auth\User`
* **原因**：Model 类继承自外部动态扩展基类，在 AOT 全局类型推断时要求基类在编译期提供静态符号。
* **解决**：在 `project.yml` 中明确指定静态编译源码范围，将核心 CLI、业务控制器与基础库进行 AOT 原生编译。

### 二、Monolog 3 穿透性 switch 语句适配
* **现象**：`Fatal error: switch case must end with return/break/continue/exit/throw, Stmt_Expression given in Utils.php:226`
* **原因**：TypePHP AOT 严格要求 switch case 分支必须以 jump 语句结束，不允许隐式 fallthrough。
* **解决**：将 `Utils::expandIniShorthandBytes` 的 switch fallthrough 重构为显式的 `if-elseif`。

### 三、分发包运行环境依赖隔离
* **现象**：执行二进制时报 `ClassLoader.php: Failed to open stream`。
* **原因**：独立运行目录需要配套的 `bootstrap`、`app`、`composer.json` 等结构。
* **解决**：在 `package.bat` 中自动同步运行时依赖结构，并通过 `run.bat` 注入独立 `PHPRC` 与 `PATH`。
