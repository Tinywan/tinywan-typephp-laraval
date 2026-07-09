<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Shop
 *
 * @property int $id
 * @property string|null $app_id
 * @property string $name 店铺名称
 * @property string $name_desc 店铺名称描述
 * @property string $full_name 店铺全称
 * @property string $domain_url 域名
 * @property int $admin_id 管理员
 * @property string $sales 销售员
 * @property string $describe 描述
 * @property string $logo logo
 * @property string $service_img 客服
 * @property string $complaint_service_img 投诉客服二维码
 * @property string $enterprise_qr_logo 企微logo
 * @property string $service_background_img 客服背景图
 * @property string $service_sale_wechat_ids 默认关联销售
 * @property string $comment_block_words 评论屏蔽词
 * @property int $is_sync_goods 是否自动同步商品信息 0否 1是
 * @property int $home_page_rule 首页展示规则 0横向分类 1纵向分类
 * @property int $home_special_rule 首页特卖展示规则 0不展示 1展示
 * @property string $home_factory_name 首页 工厂好货名字
 * @property string $home_special_name 首页 品牌特卖名字
 * @property int $show_style_type 店铺风格 0-红色 1-橙色 2-绿色 3-浅红色
 * @property string $secret 微信secret
 * @property string $body 微信主体
 * @property string $pay_system 支付系统
 * @property string $adapay_config adapay对应配置
 * @property string $adapay_appid adapay应用id
 * @property string $yeepay_config yeepay对应配置文件
 * @property string $yeepay_appid yeepay应用id
 * @property string $wechatpay_config wechatpay对应配置文件
 * @property string $wechatpay_appid wechatpay应用id
 * @property int $bind_supplier_id 绑定供应商id
 * @property string $pay_style 支付方式 normal-普通 jump_mini_half-半屏跳转
 * @property string $div_mode 分账方式 real-实时不分账；delay-延迟分账；real_settle-实时分账；virtual_delay-虚拟延迟分账
 * @property string $share_title 分享文案
 * @property string $share_image 分享图片
 * @property string $official_app_id 公众号appid
 * @property string $official_link 公众号文章链接
 * @property string $background_image 商品详情页背景图
 * @property string $user_background_img 用户详情页背景图
 * @property int $vip_on 是否开启会员功能 0否 1是
 * @property string $vip_name 会员名称
 * @property int $vip_type 会员类型 1:按月30天 2:按年365天
 * @property string $vip_price 会员价格
 * @property string $vip_share_image 会员分享图
 * @property string $vip_share_title 会员分享标题
 * @property int $vip_price_show 是否展示会员价 0否 1是
 * @property int $share_on 是否开启分享送会员功能 0否 1是
 * @property int $share_inviter_days 邀请下单人送会员天数
 * @property int $share_invitee_days 被邀请人送会员天数
 * @property int $share_shop_goods_id 分享店铺商品id
 * @property int $vip_detail_on 是否开启会员详情页功能 0否 1是
 * @property string|null $vip_detail_image 会员详情页图片
 * @property string $vip_detail_share_image 会员详情页分享图
 * @property string $vip_detail_share_title 会员详情页分享标题
 * @property int $deduct_overtime_ship_on 是否开启超时发货扣款 0否 1是
 * @property int $deduct_deceit_ship_on 是否开启虚假发货扣款 0否 1是
 * @property string $sender_consignee 寄件方姓名
 * @property string $sender_mobile 寄件方手机号
 * @property string $sender_address 寄件方地址
 * @property int $show_stock 是否显示库存 0否 1是
 * @property int $refund_mode 退款模式 1-用户申请 2-跳销售二维码
 * @property int $refund_pickup_on 是否开启上门取件 0否 1是
 * @property int $show_identity 是否显示实名认证 0否 1是
 * @property int $show_explain_video 是否显示讲解视频 0否 1是
 * @property int $score_is_setting 是否配置积分商城 0否 1是
 * @property int $score_on 是否开启积分商城 0否 1是
 * @property string $score_subscribe 订阅奖励积分
 * @property string $score_trade 消费奖励积分 单位：元
 * @property string $score_trade_share 跟团后分享至群聊奖励积分
 * @property string $score_comment 团员晒单奖励积分
 * @property string $score_invite_new_user 团员邀新奖励积分
 * @property int $score_cash 积分抵扣现金开关
 * @property string $oss_driver oss驱动 aliyun-阿里云
 * @property int $oss_is_use oss配置是否可用
 * @property string $oss_access_id oss配置 access_id
 * @property string $oss_access_key oss配置 access_key
 * @property string $oss_endpoint oss 上传节点
 * @property string $oss_url oss 访问节点
 * @property string $oss_bucket oss bucket
 * @property int $banner_icon 图标广告形式 0-一行四个; 1-一行五个
 * @property int $banner_rank 排行榜单形式 0-一行一个; 1-一行两个
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $bind_copy_shop_id 绑定复制的店铺ID
 * @property string $score_cash_amount 每100积分抵扣多少元现金
 * @property string $score_cash_rate 积分抵扣销售佣金最大比例
 * @property string|null $score_tips 积分说明
 * @property string $score_cash_limit 单次积分抵扣金额上限
 * @property int|null $vip_join_score 会员赠送积分
 * @property int $shop_group_share_type 类型 1单图 2超市图 3组合图 4默认跟团图 5默认跟团图底图
 * @property int $shop_status 店铺状态 1开启 0关闭
 * @property string|null $pickup_default_type 默认取件类型: jd 京东 kdn 快递鸟
 * @property int $sale_profit_show_type 佣金显示类型 0不显示 1正常显示
 * @property string|null $vip_price_standard 会员价标准
 * @property string|null $score_limit_config 积分限制配置
 * @property string $deduct_overtime_coupon_use_style 使用方式:less_com-小于佣金,marketing_spend-营销账户支出
 * @property string $deduct_deceit_coupon_use_style 使用方式:less_com-小于佣金,marketing_spend-营销账户支出
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAdapayAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAdapayConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBackgroundImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBannerIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBannerRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBindCopyShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBindSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCommentBlockWords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereComplaintServiceImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeductDeceitCouponUseStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeductDeceitShipOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeductOvertimeCouponUseStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeductOvertimeShipOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDescribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDivMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDomainUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereEnterpriseQrLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereHomeFactoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereHomePageRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereHomeSpecialName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereHomeSpecialRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereIsSyncGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereNameDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOfficialAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOfficialLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssAccessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssAccessKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssBucket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssIsUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOssUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop wherePayStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop wherePaySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop wherePickupDefaultType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereRefundMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereRefundPickupOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSaleProfitShowType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreCash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreCashAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreCashLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreCashRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreInviteNewUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreIsSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreLimitConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreSubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreTrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereScoreTradeShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSenderAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSenderConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSenderMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereServiceBackgroundImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereServiceImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereServiceSaleWechatIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShareImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShareInviteeDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShareInviterDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShareOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShareShopGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShareTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShopGroupShareType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShopStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShowExplainVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShowIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShowStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShowStyleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUserBackgroundImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipDetailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipDetailOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipDetailShareImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipDetailShareTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipJoinScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipPriceShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipPriceStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipShareImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipShareTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereVipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereWechatpayAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereWechatpayConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereYeepayAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereYeepayConfig($value)
 * @mixin \Eloquent
 */
class Shop extends Model
{
}
