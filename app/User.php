<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\User
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $is_member 是否会员 1会员 0不是会员
 * @property string $nickname 昵称
 * @property string $name 姓名
 * @property string $openid openid
 * @property string $unionid unionid
 * @property string $unionid_open 妙选开放平台unionid
 * @property string $official_openid 公众号openid
 * @property string $avatar 用户头像
 * @property string $mobile 手机号
 * @property int $sex 性别：1男性，2女性，0未知
 * @property string|null $birthday 生日
 * @property string $province 所在省份
 * @property string $city 所在城市
 * @property string $country 所在国家
 * @property string $remark 备注
 * @property string $nickname_remark 昵称备注
 * @property int $role 类型 0普通用户 1销售
 * @property int $sale_id 所属分销员 对应后台用户id
 * @property int $sale_wechat_id 所属分销员 对应销售微信id
 * @property int $sale_user_id 所属分销员 对应小程序用户id
 * @property int $bind_sales_wechat_id 永久绑定运营微信id
 * @property string|null $vip_start_time 会员开始时间
 * @property string|null $vip_end_time 会员结束时间
 * @property int $inviter_id 邀请人id
 * @property string $score 积分
 * @property int $is_authorized 是否授权 0未授权 1已授权
 * @property int $is_subscribe 是否关注公众号 0否 1是
 * @property int $is_subscribe_group 是否订阅开团提醒（模版消息） 0否 1是
 * @property string $external_userid 企业微信外部联系人userid
 * @property string|null $add_crop_time 添加企业微信时间
 * @property string $source 来源
 * @property int $status 用户状态 0正常 1拉黑
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $need_upload_user_id
 * @property string $status_change_reason 拉黑原因
 * @property string|null $password 用户密码
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddCropTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBindSalesWechatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExternalUserid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInviterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAuthorized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsSubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsSubscribeGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNeedUploadUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNicknameRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOfficialOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSaleUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSaleWechatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatusChangeReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUnionidOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVipEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVipStartTime($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
