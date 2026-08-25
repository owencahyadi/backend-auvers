<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @method static void addGlobalScope(string $identifier, \Closure $scope)
 * @method static void creating(\Closure|string|array $callback)
 */
trait BelongsToStore
{
    public static function bootBelongsToStore()
    {
        // 1. AUTO FILTER (SELECT)
        static::addGlobalScope('store', function (Builder $builder) {
            if (app()->bound('active_store_id')) {
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.store_id', app('active_store_id'));
            }
        });

        // 2. AUTO INSERT (CREATE/SAVE)
        static::creating(function ($model) {
            if (app()->bound('active_store_id') && empty($model->store_id)) {
                $model->store_id = app('active_store_id');
            }
        });
    }
}