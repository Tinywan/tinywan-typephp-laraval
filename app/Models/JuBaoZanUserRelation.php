<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JuBaoZanUserRelation
 *
 * @property int $id
 * @property int $user_id 系统用户ID
 * @property string $jubaozan_agentid 聚宝赞会员ID
 * @property string $jubaozan_orderid 聚宝赞会员编号
 * @property string $unionid 微信unionid
 * @property string $mobile 手机号
 * @property int $shop_id 店铺ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereJubaozanAgentid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereJubaozanOrderid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JuBaoZanUserRelation whereUserId($value)
 * @mixin \Eloquent
 */
class JuBaoZanUserRelation extends Model
{
    protected $table = 'jubaozan_user_relations';
}
