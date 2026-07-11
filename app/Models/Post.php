<?php

namespace App\Models;

use App\Scopes\AgeScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $user_id
 * @mixin \Eloquent
 */
class Post extends Model
{
    /**
     * 本地作用域。将查询范围限制为仅包含状态为0的帖子
     * @param $query
     * @return mixed
     */
//    public function scopeStatus($query)
//    {
//        return $query->where('status', 1);
//    }

    public function scopeStatus($query,$value)
    {
        return $query->where('status', $value);
    }

    /**
     * @desc  启用全局作用域
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new AgeScope());

        /** 匿名函数 允许您使用闭包定义全局范围，这对于不需要单独类的简单范围特别有用 */
//        static::addGlobalScope('age', function (Builder $builder) {
//            $builder->where('age', '>', 200);
//        });
    }

    //将虚拟字段追加到数据对象列表里去
    protected $appends = ['author'];

    public function getAuthorAttribute(): string
    {
        // 修改后
//        return $this->name.' 作者是 ：' .$this->age;
        return $this->attributes['name'].'-'.$this->attributes['age'];
    }
}
