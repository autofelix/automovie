<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ForumComments extends Model
{
    protected $table = 'forum_comments';
}
