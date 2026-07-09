<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JuBaoZanAccessToken
 *
 * @property int $id
 * @property int $shop_id 店铺ID
 * @property string $client_id 应用ID
 * @property string $access_token 访问令牌
 * @property int $expires_in 过期时间
 * @property string $token_type 令牌类型
 * @property string $refresh_token 刷新令牌
 * @property int $shopId 授权方ID
 * @property string $platform 授权方类型;JBZ:商家SUP:供应商
 * @property int $status 启用状态
 * @property string|null $subscribed_events 已订阅事件列表
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereSubscribedEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereTokenType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanAccessToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class JuBaoZanAccessToken extends Model
{
    protected $table = 'jubaozan_access_tokens';
}
