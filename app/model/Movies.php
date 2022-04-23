<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Movies extends Model
{
    protected $table = 'movies';
}
