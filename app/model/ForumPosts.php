<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ForumPosts extends Model
{
    protected $table = 'forum_posts';
}
