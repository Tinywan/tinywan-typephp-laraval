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

## 后台 JWT 与文章授权流程

后台接口使用 `admin` guard，配置为 JWT 认证。访问 `/admin/v1/*` 下面的接口前，需要先登录获取 token。

### 1. 登录获取 token

```http
POST /admin/login
Accept: application/json
Content-Type: application/json

{
    "email": "admin@test.com",
    "password": "123456"
}
```

登录成功后会返回：

```json
{
    "code": 0,
    "msg": "登录成功",
    "data": {
        "access_token": "token内容",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

后续请求后台接口时，需要在请求头中携带 token：

```http
Authorization: Bearer token内容
Accept: application/json
```

### 2. 为什么更新文章要传 id

例如文章更新接口：

```http
GET /admin/v1/post/update/1
Authorization: Bearer token内容
Accept: application/json
```

这里的 `1` 是文章 ID。控制器会先通过 ID 查询文章：

```php
$post = Post::findOrFail($id);
```

如果文章不存在，Laravel 会返回 404。

### 3. Policy 负责判断是否有权限

认证只解决“当前用户是谁”，不解决“当前用户能不能操作这篇文章”。

控制器中调用：

```php
$this->authorize('update', $post);
```

Laravel 会把当前登录用户和文章对象传给 `PostPolicy`：

```php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id;
}
```

含义是：只有文章作者本人才能更新这篇文章。

简单理解：

```text
JWT token       判断你是谁
文章 ID         判断你要操作哪篇文章
PostPolicy      判断你是否有权限操作这篇文章
```
