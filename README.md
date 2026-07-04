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
