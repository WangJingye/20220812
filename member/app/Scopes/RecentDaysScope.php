<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RecentDaysScope implements Scope
{
    /**
     * 将范围应用于给定的 Eloquent 查询生成器
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $now = date('Y-m-d H:i:s', strtotime("-1 year"));
        return $builder->where('received_at', '>', $now);
    }
}