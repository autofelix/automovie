<?php
declare (strict_types=1);

namespace app\index\controller;

use think\facade\View;

use app\model\Movies;

class Sort
{
    public function popular()
    {
        $movies = Movies::order('views', 'desc')
            ->order('publish_date', 'desc')
            ->paginate(24);

        return View::fetch('index@index', compact('movies'));
    }

    public function sowar()
    {
        $movies = Movies::where(['type' => 0])
            ->order('publish_date', 'desc')
            ->paginate(24);

        return View::fetch('index@index', compact('movies'));
    }

    public function infantry()
    {
        $movies = Movies::where(['type' => 1])
            ->order('publish_date', 'desc')
            ->paginate(24);

        return View::fetch('index@index', compact('movies'));
    }

    public function magnetic()
    {
        $movies = Movies::where(['is_magnetic' => 1])
            ->order('publish_date', 'desc')
            ->paginate(24);

        return View::fetch('index@index', compact('movies'));
    }

    public function hd()
    {
        $movies = Movies::where(['is_hd' => 1])
            ->order('publish_date', 'desc')
            ->paginate(24);

        return View::fetch('index@index', compact('movies'));
    }

    public function subtitle()
    {
        $movies = Movies::where(['is_subtitle' => 1])
            ->order('publish_date', 'desc')
            ->paginate(24);

        return View::fetch('index@index', compact('movies'));
    }
}
