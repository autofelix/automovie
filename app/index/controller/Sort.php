<?php
declare (strict_types=1);

namespace app\index\controller;

use think\facade\View;

use app\model\Movies;
use app\model\Stars;

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
        $movies = Movies::where([
            'type' => 1,
            'is_hd' => 1,
            'is_magnetic' => 1
        ])
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

    public function star_sowar_avatar()
    {
        $stars = Stars::where([
            'type' => 0
        ])
            ->where('avatar', '<>', 'nowprinting.gif')
            ->paginate(60);

        return View::fetch('index@star', compact('stars'));
    }

    public function star_sowar_height()
    {
        $stars = Stars::where([
            'type' => 0
        ])
            ->order('height', 'desc')
            ->paginate(60);

        return View::fetch('index@star', compact('stars'));
    }

    public function star_sowar_cupsize()
    {
        $stars = Stars::where([
            'type' => 0
        ])
            ->order('cupsize', 'desc')
            ->paginate(60);

        return View::fetch('index@star', compact('stars'));
    }

    public function star_infantry_avatar()
    {
        $stars = Stars::where([
            'type' => 1
        ])
            ->where('avatar', '<>', 'nowprinting.gif')
            ->paginate(60);

        return View::fetch('index@infantry', compact('stars'));
    }

    public function star_infantry_height()
    {
        $stars = Stars::where([
            'type' => 1
        ])
            ->order('height', 'desc')
            ->paginate(60);

        return View::fetch('index@infantry', compact('stars'));
    }

    public function star_infantry_cupsize()
    {
        $stars = Stars::where([
            'type' => 1
        ])
            ->order('cupsize', 'desc')
            ->paginate(60);

        return View::fetch('index@infantry', compact('stars'));
    }
}
