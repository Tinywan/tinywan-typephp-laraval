## Nginx 配置

```conf
server {
    listen 80;
    server_name local.laravel.tinywan.com;
    root /var/www/chunfen/v6.laravel.tinywan.com/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ /\. {
        deny all;
    }

    location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        try_files $uri =404;
    }

    location ~* .(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1h;
        add_header Cache-Control public;
        add_header Pragma public;
        add_header Vary Accept-Encoding;
    }

    location ~ \.php$ {
        try_files $fastcgi_script_name =404;
        include fastcgi_params;
        fastcgi_pass php74:9000;
        fastcgi_index index.php;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_intercept_errors on;
        fastcgi_buffers 8 16k;
        fastcgi_buffer_size 32k;
        fastcgi_param SCRIPT_FILENAME  $document_root$fastcgi_script_name;
    }

    # security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src * data: 'unsafe-eval' 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    # gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;
}
```

## 事件与监听器使用说明

Laravel 事件机制适合把“业务动作已经发生”和“动作发生后需要做什么”拆开。比如用户创建成功后，创建用户的代码只负责触发 `UserCreatedEvent`，发送通知、同步第三方平台、写业务日志等后续动作交给监听器处理。

这样做的好处是控制器或服务层不用堆积大量后置逻辑，后续新增一个动作时，只需要新增监听器并注册到事件上。

### 1. 使用场景

事件适合表达已经发生的业务事实，例如：

```text
用户创建成功
用户登录成功
用户退出登录
订单支付成功
商品库存变化
```

监听器适合处理事件发生后的动作，例如：

```text
发送通知
同步第三方系统
记录业务日志
发放优惠券
清理缓存
```

当前项目已经有以下事件和监听器：

```text
app/Events/UserCreatedEvent.php
app/Events/UserLoginEvent.php
app/Events/UserLogoutEvent.php

app/Listeners/SendUserNotificationListener.php
app/Listeners/JuBaoZanUserCreatedListener.php
app/Listeners/UserEventSubscriber.php
```

### 2. 定义事件

事件类放在 `app/Events` 目录。事件对象主要负责携带上下文数据，例如用户对象、订单对象、操作人 ID 等。

用户创建事件示例：

```php
namespace App\Events;

use App\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
```

重点：

```text
Dispatchable      允许使用 UserCreatedEvent::dispatch($user) 触发事件
SerializesModels  队列化事件或监听器时，帮助序列化 Eloquent 模型
public $user      监听器中可以通过 $event->user 获取用户对象
```

### 3. 定义监听器

监听器放在 `app/Listeners` 目录，通过 `handle()` 方法接收事件对象。

发送用户通知监听器示例：

```php
namespace App\Listeners;

use App\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Log;

class SendUserNotificationListener
{
    public function handle(UserCreatedEvent $event)
    {
        $user = $event->user;

        Log::info('[发送用户通知] 监听用户创建事件', [
            'user' => $user,
        ]);
    }
}
```

同步第三方平台监听器示例：

```php
namespace App\Listeners;

use App\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Log;

class JuBaoZanUserCreatedListener
{
    public function handle(UserCreatedEvent $event)
    {
        $user = $event->user;

        Log::info('[聚宝赞用户同步] 监听用户创建事件', [
            'user' => $user,
        ]);
    }
}
```

一个事件可以对应多个监听器。每个监听器只做一类事情，后续维护会更清晰。

### 4. 注册事件和监听器

事件和监听器的绑定关系配置在 `app/Providers/EventServiceProvider.php`。

当前项目中的用户创建事件注册方式：

```php
use App\Events\UserCreatedEvent;
use App\Listeners\JuBaoZanUserCreatedListener;
use App\Listeners\SendUserNotificationListener;

protected $listen = [
    UserCreatedEvent::class => [
        SendUserNotificationListener::class,
        JuBaoZanUserCreatedListener::class,
    ],
];
```

含义是：只要触发 `UserCreatedEvent`，Laravel 会依次执行数组中的监听器。

```text
UserCreatedEvent
    -> SendUserNotificationListener::handle()
    -> JuBaoZanUserCreatedListener::handle()
```

如果要新增一个用户创建后的动作，例如写审计日志，可以新增监听器后注册进去：

```php
protected $listen = [
    UserCreatedEvent::class => [
        SendUserNotificationListener::class,
        JuBaoZanUserCreatedListener::class,
        WriteUserCreatedAuditLogListener::class,
    ],
];
```

### 5. 触发事件

在业务动作完成后触发事件。常见写法是使用 `event()` 辅助函数：

```php
use App\Events\UserCreatedEvent;
use App\User;

$user = User::create($data);

event(new UserCreatedEvent($user));
```

也可以使用事件类自带的 `dispatch()` 方法：

```php
UserCreatedEvent::dispatch($user);
```

两种写法效果一致。触发后，Laravel 会根据 `EventServiceProvider::$listen` 找到对应监听器并执行。

当前项目的登录事件在 `routes/admin.php` 中触发：

```php
use App\Events\UserLoginEvent;

Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    $token = auth('admin')->attempt($credentials);

    if (!$token) {
        return response_json('账号或密码错误', 401, [], 401);
    }

    event(new UserLoginEvent(auth('admin')->user()));

    return response_json('登录成功', 0, [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::guard('admin')->factory()->getTTL() * 60,
    ]);
});
```

### 6. 使用事件订阅者

如果多个事件属于同一类业务，可以使用订阅者集中管理。当前项目使用 `UserEventSubscriber` 统一订阅用户登录和用户退出事件。

订阅者示例：

```php
namespace App\Listeners;

use App\Events\UserLoginEvent;
use App\Events\UserLogoutEvent;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber
{
    public function handleUserLogin($event)
    {
        Log::info('[事件订阅] User logged in:', [
            'user_id' => $event->user->id,
        ]);
    }

    public function handleUserLogout($event)
    {
        Log::info('[事件订阅] User logged out:', [
            'user_id' => $event->user->id,
        ]);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            UserLoginEvent::class,
            'App\Listeners\UserEventSubscriber@handleUserLogin'
        );

        $events->listen(
            UserLogoutEvent::class,
            'App\Listeners\UserEventSubscriber@handleUserLogout'
        );
    }
}
```

订阅者需要在 `EventServiceProvider` 的 `$subscribe` 中注册：

```php
use App\Listeners\UserEventSubscriber;

protected $subscribe = [
    UserEventSubscriber::class,
];
```

普通监听器适合一个事件对应一个或多个独立动作；订阅者适合把同一业务域的一组事件集中放在一个类里。

### 7. 同步监听和队列监听

默认监听器是同步执行的。当前 `SendUserNotificationListener` 没有实现 `ShouldQueue`，所以触发事件后会立即执行监听器。

```text
创建用户
    -> 触发 UserCreatedEvent
    -> 立即执行 SendUserNotificationListener::handle()
    -> 立即执行 JuBaoZanUserCreatedListener::handle()
    -> 监听器执行完成后，请求继续往下执行
```

同步监听器不需要启动队列 worker。

如果监听器里有发送短信、发送邮件、调用第三方接口等耗时操作，可以改成队列监听器：

```php
namespace App\Listeners;

use App\Events\UserCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserNotificationListener implements ShouldQueue
{
    public function handle(UserCreatedEvent $event)
    {
        $user = $event->user;

        // 发送短信、邮件或调用第三方接口
    }
}
```

队列监听器需要配置 `.env` 中的队列连接，并启动 worker：

```env
QUEUE_CONNECTION=redis
```

```bash
php artisan queue:work
```

开发环境也可以使用同步队列：

```env
QUEUE_CONNECTION=sync
```

但 `sync` 会在当前请求内直接执行，不会异步进入后台 worker。

### 8. 生成事件和监听器

可以先在 `EventServiceProvider::$listen` 中写好映射关系，再执行：

```bash
php artisan event:generate
```

Laravel 会根据映射关系自动生成不存在的事件类和监听器类。

也可以手动生成：

```bash
php artisan make:event UserCreatedEvent
php artisan make:listener SendUserNotificationListener
```

如果监听器需要直接绑定某个事件，可以使用：

```bash
php artisan make:listener SendUserNotificationListener --event=UserCreatedEvent
```

### 9. 完整调用链路

用户创建事件链路：

```text
User::create($data)
    -> event(new UserCreatedEvent($user))
    -> EventServiceProvider::$listen
    -> SendUserNotificationListener::handle($event)
    -> JuBaoZanUserCreatedListener::handle($event)
```

用户登录订阅链路：

```text
POST /admin/login
    -> auth('admin')->attempt($credentials)
    -> event(new UserLoginEvent(auth('admin')->user()))
    -> EventServiceProvider::$subscribe
    -> UserEventSubscriber::subscribe()
    -> UserEventSubscriber::handleUserLogin($event)
```

### 10. 常见问题

事件触发了但监听器没有执行：

```text
检查 EventServiceProvider::$listen 是否配置了事件和监听器映射
检查 EventServiceProvider::$subscribe 是否注册了订阅者
检查事件类和监听器类的 namespace 是否正确
检查触发事件的代码是否真的执行到了
```

监听器拿不到用户：

```text
检查触发事件时是否传入了 $user
检查事件构造方法是否把 $user 保存到了 public $user
检查监听器 handle(UserCreatedEvent $event) 类型是否正确
```

队列监听器没有执行：

```text
检查监听器是否 implements ShouldQueue
检查 .env 的 QUEUE_CONNECTION 是否正确
检查是否启动了 php artisan queue:work
检查 failed_jobs 表或日志中是否有失败记录
```

监听器中代码执行很慢：

```text
耗时操作不要放在同步监听器里
把监听器改成 implements ShouldQueue
确认队列 worker 已经运行
```

## 后台认证、授权、控制器、模型与策略流程

后台接口当前使用 Laravel 的 `admin` guard，`admin` guard 使用 JWT 驱动。完整流程可以分成两步：先认证拿 token，再携带 token 访问受保护接口并做权限判断。

### 1. 相关角色说明

```text
Route               定义接口地址、中间件和控制器入口
auth:admin          JWT 认证中间件，判断 token 是否有效
check.admin         自定义后台中间件，可继续判断是否后台管理员
Controller          接收请求、查询模型、调用授权、执行业务
Model               对应数据库表，例如 Post 对应 posts 表
Policy              策略类，集中判断当前用户是否能操作某个模型
TinywanHandler      自定义异常处理器，可把异常统一转成 JSON
```

认证和授权是两个概念：

```text
认证 Authentication：判断你是谁
授权 Authorization：判断你能不能做这件事
```

例如：

```text
JWT token 有效           说明你已经登录
user_id 等于 post.user_id 说明你是这篇文章作者
```

### 2. 登录认证流程

登录接口不应该放在 `auth:admin` 中间件里面，因为用户登录前还没有 token。

```php
Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (!$token = Auth::guard('admin')->attempt($credentials)) {
        return response_json('账号或密码错误', 401, [], 401);
    }

    return response_json('登录成功', 0, [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::guard('admin')->factory()->getTTL() * 60,
    ]);
});
```

请求示例：

```http
POST /admin/login
Accept: application/json
Content-Type: application/json

{
    "email": "admin@test.com",
    "password": "123456"
}
```

`attempt($credentials)` 的判断过程：

```text
1. 根据 config/auth.php 找到 admin guard
2. admin guard 使用 jwt driver
3. jwt guard 使用 users provider
4. users provider 使用 App\User 模型查询 users 表
5. 根据 email 查询用户
6. 使用 Hash::check() 判断请求密码和数据库 password 是否匹配
7. 匹配成功，生成 JWT token
8. 匹配失败，返回 false
```

因此 `attempt()` 返回 `false` 常见原因是：

```text
users 表中没有这个 email
请求 password 不正确
数据库 password 不是 Hash::make() 生成的哈希值
App\User 没有实现 JWTSubject
JWT_SECRET 未配置或配置缓存未刷新
```

`App\User` 必须实现 `JWTSubject`：

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

### 3. 携带 token 访问后台接口

受保护路由放在 `auth:admin` 中间件后面：

```php
Route::prefix('v1')
    ->middleware([
        'auth:admin',
        'check.admin',
    ])
    ->group(function () {
        Route::prefix('post')->group(function () {
            Route::get('update/{id}', [PostController::class, 'update']);
        });
    });
```

访问时必须携带 token：

```http
GET /admin/v1/post/update/1
Authorization: Bearer token内容
Accept: application/json
```

请求进入后的中间件判断顺序：

```text
1. 匹配 /admin/v1/post/update/{id} 路由
2. 进入 auth:admin 中间件
3. 从 Authorization 请求头解析 Bearer token
4. 校验 token 签名、有效期、用户是否存在
5. token 无效，抛出未认证异常
6. token 有效，把当前用户绑定到 admin guard
7. 进入 check.admin 中间件
8. 中间件通过后进入 PostController@update
```

拿当前登录用户：

```php
Auth::guard('admin')->user();
auth('admin')->user();
auth('admin')->id();
```

### 4. 控制器负责查询模型和触发授权

当前文章更新控制器逻辑：

```php
public function update(int $id)
{
    $post = Post::findOrFail($id);

    $this->authorize('update', $post);

    return response_json('更新成功');
}
```

这里分两步：

```text
Post::findOrFail($id)       根据文章 ID 查询数据库
$this->authorize(...)       判断当前用户是否有权限更新这篇文章
```

如果文章不存在：

```php
Post::findOrFail($id);
```

会抛出 `ModelNotFoundException`，通常返回 404。

如果文章存在，但当前用户没有权限：

```php
$this->authorize('update', $post);
```

会抛出 `AuthorizationException`，通常返回 403。

### 5. 模型负责对应数据库表

文章模型：

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
}
```

Laravel 默认会把模型名转成复数表名：

```text
Post  -> posts
User  -> users
```

所以 `Post::findOrFail($id)` 默认查询的是 `posts` 表。

如果表名不是 `posts`，需要在模型中指定：

```php
class Post extends Model
{
    protected $table = 'post';
}
```

Policy 判断中用到：

```php
$post->user_id
```

所以 `posts` 表中应该有 `user_id` 字段，用来表示这篇文章属于哪个用户。

### 6. Policy 负责集中写权限规则

策略注册在 `AuthServiceProvider`：

```php
protected $policies = [
    Post::class => PostPolicy::class,
];
```

当控制器调用：

```php
$this->authorize('update', $post);
```

Laravel 会根据 `$post` 的模型类型找到 `PostPolicy`，然后调用：

```php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id;
}
```

参数来源：

```text
$user 来自 auth:admin 认证后的当前登录用户
$post 来自控制器传入的 Post 模型对象
```

这个判断表示：

```text
当前登录用户 ID 等于文章 user_id，允许更新
否则拒绝更新
```

如果返回 `true`，控制器继续往下执行。

如果返回 `false`，Laravel 抛出授权异常，接口返回 403。

### 7. 完整调用链路

```text
POST /admin/login
    -> Auth::guard('admin')->attempt()
    -> 校验 email 和 password
    -> 生成 JWT token

GET /admin/v1/post/update/1
    -> 路由匹配 update/{id}
    -> auth:admin 校验 Bearer token
    -> check.admin 后台权限中间件
    -> PostController@update($id)
    -> Post::findOrFail($id)
    -> $this->authorize('update', $post)
    -> PostPolicy::update($user, $post)
    -> 判断 $user->id === $post->user_id
    -> 允许则返回更新成功
    -> 拒绝则返回 403
```

### 8. 常见问题

访问 `/admin/v1/post/update` 不带 ID：

```text
路由不匹配，因为当前路由是 /admin/v1/post/update/{id}
```

访问接口不带 token：

```text
auth:admin 会拦截，请求不会进入控制器
```

token 有效但不是文章作者：

```text
请求会进入控制器，但会被 PostPolicy 拒绝
```

`attempt()` 返回 `false`：

```text
说明账号密码认证失败，不是 token 生成失败
优先检查 email 是否存在，以及 password 是否为 Hash::make() 生成
```

接口返回 HTML 而不是 JSON：

```text
请求头加 Accept: application/json
或者启用 TinywanHandler 对 /admin/* 统一返回 JSON
```
