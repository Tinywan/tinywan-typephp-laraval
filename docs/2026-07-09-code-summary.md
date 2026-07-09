# 2026-07-09 代码整理记录

本文档整理 2026-07-09 开发的主要代码改动，方便后续继续完善接口、排查问题和提交代码。

## 1. 用户端路由注册

新增并注册了用户端独立路由文件 `routes/user.php`。

运行时路由前缀：

```text
/user
```

注册位置：

```php
// app/Providers/RouteServiceProvider.php
$this->mapUserRoutes();

protected function mapUserRoutes()
{
    Route::prefix('user')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/user.php'));
}
```

当前已注册接口：

```text
POST /user/auth/login
GET  /user/v1/order/index
```

验证命令：

```bash
docker exec -w /var/www/chunfen/v6.laravel.tinywan.com dnmp-php74 php artisan route:list --path=user
```

## 2. 小程序用户登录接口

接口地址：

```http
POST /user/auth/login
```

请求头要求：

```http
appid: 小程序 app_id
Accept: application/json
```

路由定义：

```php
Route::post('auth/login', [AuthController::class, 'login'])
    ->middleware('check.mini');
```

核心处理流程：

```text
1. check.mini 中间件读取请求头 appid
2. 根据 appid 查询 shops 表，获取当前店铺
3. 将 shop 合并到 Request
4. AuthController@login 根据 shop_id 和 openid 查询用户
5. 使用 mini guard 生成 JWT token
6. 触发 UserCreatedEvent 事件
7. 返回 access_token、token_type、expires_in
```

当前控制器位置：

```text
app/Http/Controllers/User/v1/AuthController.php
```

当前代码中 `openid` 还是写死的测试值：

```php
$openid = 'o7R6n4o0kq7BKTWhUPk5a-srKhxk';
```

后续需要改成从小程序登录凭证换取真实 `openid`。

## 3. mini JWT guard

在 `config/auth.php` 中新增了 `mini` guard：

```php
'mini' => [
    'driver' => 'jwt',
    'provider' => 'users',
    'hash' => false,
],
```

使用方式：

```php
$ttl = 14 * 24 * 60;
$token = auth('mini')->setTTL($ttl)->login($user);
```

返回过期时间单位：

```text
setTTL 使用分钟
接口 expires_in 返回秒
```

## 4. 小程序店铺校验中间件

已注册中间件别名：

```php
// app/Http/Kernel.php
'check.mini' => \App\Http\Middleware\CheckMiniUserMiddleware::class,
```

中间件文件：

```text
app/Http/Middleware/CheckMiniUserMiddleware.php
```

职责：

```text
1. 读取请求头 appid
2. appid 缺失时返回错误
3. 使用缓存 USER:MINI:{appid} 查询店铺
4. 找不到店铺时返回 401
5. 找到店铺后合并 shop 到 Request
```

缓存 key：

```text
USER:MINI:{appid}
```

注意：当前项目里还有一个未注册的 `app/Http/Middleware/CheckMini.php`，逻辑与 `CheckMiniUserMiddleware` 接近，后续建议二选一保留，避免维护两套相似代码。

## 5. 店铺模型

新增模型：

```text
app/Models/Shop.php
```

表名：

```php
protected $table = 'shops';
```

当前主要用于：

```text
1. check.mini 根据 app_id 查询店铺
2. check.admin 根据 Shop-Id 查询店铺
3. UserCreatedEvent 携带 shop 上下文
```

## 6. 后台接口上下文增强

`AdminUserMiddleware` 增加了店铺上下文处理。

请求头：

```http
Shop-Id: 店铺 ID
```

处理结果：

```php
$request->merge([
    'x-site-domain' => 'shop.tinywan.com',
    'x-site-code' => 'shop',
    'shop' => $this->shop,
    'shop_id' => $this->shopId,
]);
```

用途：

```text
后台受保护接口可以从 Request 中直接读取 shop 和 shop_id。
```

## 7. 用户事件和监听器

`UserCreatedEvent` 增加了店铺上下文：

```php
public $user;
public $shop;

public function __construct(User $user, Shop $shop)
{
    $this->user = $user;
    $this->shop = $shop;
}
```

监听器 `JuBaoZanUserCreatedListener` 现在可以同时读取用户和店铺：

```php
$user = $event->user;
$shop = $event->shop;
```

日志内容调整为记录：

```text
user.nickname
shop.name
```

事件订阅器 `UserEventSubscriber` 调整了登录和退出日志文案：

```text
[事件订阅] User Login
[事件订阅] User Logout
```

## 8. 异常处理调整

`bootstrap/app.php` 将异常处理器切换为：

```php
App\Exceptions\TinywanHandler::class
```

作用：

```text
接口异常尽量返回统一 JSON 结构，而不是默认 HTML 错误页。
```

例如请求方法不匹配时：

```json
{
  "code": 405,
  "msg": "Method Not Allowed.",
  "data": {}
}
```

## 9. 订单接口占位

新增用户端订单控制器：

```text
app/Http/Controllers/User/v1/OrderController.php
```

当前接口：

```http
GET /user/v1/order/index
```

当前返回：

```json
{
  "code": 0,
  "msg": "订单列表",
  "data": {}
}
```

## 10. 当前验证结果

路由验证：

```bash
docker exec -w /var/www/chunfen/v6.laravel.tinywan.com dnmp-php74 php artisan route:list --path=user/auth
```

期望看到：

```text
POST user/auth/login App\Http\Controllers\User\v1\AuthController@login api,check.mini
```

接口验证：

```bash
curl -i -X POST http://local.laravel.tinywan.com/user/auth/login \
  -H "Accept: application/json" \
  -H "appid: 你的 appid"
```

浏览器地址栏直接访问该地址会发起 `GET` 请求，当前接口只支持 `POST`，所以返回 `405 Method Not Allowed` 是正常行为。

## 11. 后续待完善

1. `AuthController@login` 需要处理用户不存在的情况，避免 `auth('mini')->login(null)` 报错。
2. 登录接口需要接入真实小程序登录流程，用 `code` 换取 `openid`，替换当前写死的测试 `openid`。
3. `UserCreatedEvent` 当前在登录接口里触发，命名上更像“用户创建事件”。如果实际语义是登录，建议改用 `UserLoginEvent` 或新增小程序登录事件。
4. `CheckMini.php` 和 `CheckMiniUserMiddleware.php` 逻辑重复，建议保留一个。
5. `CheckMiniUserMiddleware` 中未使用的属性和 `initialize()` 方法可以清理。
6. `routes/user.php` 中用户端 `v1` 接口的认证中间件还未启用，后续需要按业务决定是否加 `auth:mini`。
7. 需要为 `POST /user/auth/login` 和 `GET /user/v1/order/index` 补充 Feature 测试。
