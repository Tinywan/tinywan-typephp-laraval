# Repository Guidelines

## 项目结构与模块组织

这是一个 Laravel 6 应用。核心 PHP 代码位于 `app/`：控制器在 `app/Http/Controllers`，中间件在 `app/Http/Middleware`，队列任务在 `app/Jobs`，导出类在 `app/Exports`，服务提供者在 `app/Providers`。Web 与 API 路由分别定义在 `routes/web.php` 和 `routes/api.php`。数据库迁移、模型工厂和填充器位于 `database/`。测试目录分为 `tests/Unit` 和 `tests/Feature`。前端源码放在 `resources/js` 与 `resources/sass`，通过 Laravel Mix 编译到 `public/js` 和 `public/css`。

## 构建、测试与开发命令

- `composer install`：安装 PHP 依赖。
- `npm install`：安装前端构建依赖。
- `php artisan serve`：启动本地 Laravel 开发服务器。
- `php artisan migrate`：执行当前环境的数据库迁移。
- `php artisan queue:work`：处理队列任务，例如 `App\Jobs\HelloJob`。
- `vendor/bin/phpunit`：运行 PHPUnit 测试套件。
- `npm run dev`：使用 Laravel Mix 编译开发环境资源。
- `npm run prod`：生成生产环境优化后的前端资源。

## 编码风格与命名约定

遵循 Laravel 与 PSR-4 约定。PHP 使用 4 空格缩进；类名使用大驼峰，例如 `UserController`、`UsersExport`；方法名使用小驼峰；路由和控制器动作应具备明确语义。控制器逻辑增长时，应将可复用逻辑拆到任务、服务、导出类或其他专用类中。JavaScript 与 Sass 应保留在 `resources/`，通过 `webpack.mix.js` 构建，不要直接维护 `public/` 下的编译产物。

## 测试指南

PHPUnit 配置位于 `phpunit.xml`。独立逻辑测试放在 `tests/Unit`，HTTP 或应用行为测试放在 `tests/Feature`。测试文件必须以 `*Test.php` 结尾，便于 PHPUnit 自动发现。测试环境默认使用内存 SQLite、array cache、array mail 和同步队列。修改应用行为后，提交前运行 `vendor/bin/phpunit`。

## 提交与 Pull Request 规范

近期提交使用简短的 Conventional Commit 风格，例如 `feat(user): 添加用户数据导出功能`、`fix: nginx config`。优先使用 `feat(scope): 摘要`、`fix(scope): 摘要`，或其他清晰类型。PR 应包含简洁说明、变更原因、受影响的路由或命令、测试结果；只有 UI 输出变化时才需要附截图。

## 安全与配置提示

不要提交 `.env` 中的真实密钥。环境差异配置应保留在 `.env`，只有安全默认值才写入 `.env.example`。Web 根目录应指向 `public/`，参考 `README.md` 中的 Nginx 配置。项目已安装 `tymon/jwt-auth`，即使当前并非所有路由都使用 JWT，认证相关改动也需要谨慎验证。
