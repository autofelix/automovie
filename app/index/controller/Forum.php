<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Request;

use think\facade\View;

class Forum
{
    public function index()
    {
        return View::fetch('index@forum');
    }
}
