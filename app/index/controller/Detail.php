<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Request;

use think\facade\View;

use app\model\Movies;
use app\model\Stars;
use app\model\Magnetics;

class Detail
{
    public function index(Request $request, $sdde)
    {
        $movie = Movies::where(['sdde' => $sdde])->find();

        $magnetics = collect();

        if ($movie->hash) {
            $magnetics = Magnetics::where(['hash' => $movie->hash])->select();
        }

        $similars = Movies::whereIn('sdde', $movie->sdde)->select();

        return View::fetch('index@detail', compact('movie', 'magnetics', 'similars'));
    }
}
