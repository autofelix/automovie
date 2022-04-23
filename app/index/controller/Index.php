<?php
declare (strict_types=1);

namespace app\index\controller;

use think\facade\View;

use app\model\Movies;

class Index
{
    public function index()
    {

        $movies = Movies::order('publish_date', 'desc')->paginate(24);

        return View::fetch('/index', compact('movies'));
    }
}
