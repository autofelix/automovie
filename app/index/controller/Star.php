<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Request;

use think\facade\View;

use app\model\Movies;
use app\model\Stars;

class Star
{
    public function sowar()
    {
        $stars = Stars::where(['type' => 0])->paginate(60);

        return View::fetch('index@star', compact('stars'));
    }

    public function infantry()
    {
        $stars = Stars::where(['type' => 1])->paginate(60);

        return View::fetch('index@infantry', compact('stars'));
    }

    public function sowar_profile(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash, 'type' => 0])->find();

        $movies = Movies::whereLike('stars', "%{$hash}%")->paginate(12);

        return View::fetch('index@sowar_profile', compact('star', 'movies'));
    }

    public function infantry_profile(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash, 'type' => 1])->find();

        $movies = Movies::whereLike('stars', "%{$hash}%")->paginate(12);

        return View::fetch('index@infantry_profile', compact('star', 'movies'));
    }

    public function sowar_alone_profile(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash, 'type' => 0])->find();

        $movies = Movies::where(['stars' => $hash])->paginate(12);

        return View::fetch('index@sowar_alone_profile', compact('star', 'movies'));
    }

    public function infantry_alone_profile(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash, 'type' => 1])->find();

        $movies = Movies::where(['stars' => $hash])->paginate(12);

        return View::fetch('index@infantry_alone_profile', compact('star', 'movies'));
    }
}
