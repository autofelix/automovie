<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Request;

use think\facade\View;

use app\model\Movies;
use app\model\Stars;

class Star
{
    public function list()
    {
        $stars = Stars::where(['type' => 0])->paginate(60);

        return View::fetch('index@star', compact('stars'));
    }

    public function infantry()
    {
        $stars = Stars::where(['type' => 1])->paginate(60);

        return View::fetch('index@star', compact('stars'));
    }

    public function profile(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash])->find();

        $movies = Movies::whereLike('stars', "%{$hash}%")->paginate(12);

        return View::fetch('index@profile', compact('star', 'movies'));
    }

    public function alone(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash])->find();

        $movies = Movies::where(['stars' => $hash])->paginate(12);

        return View::fetch('index@alone', compact('star', 'movies'));
    }
}
