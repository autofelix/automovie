<?php
declare (strict_types=1);

namespace app\index\controller;

use app\model\Directors;
use app\model\Genres;
use app\model\Producers;
use app\model\Publishers;
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

        $similars = Movies::whereIn('sdde', $movie->similars)->select();
        $stars = Stars::whereIn('hash', $movie->stars)->where(['type' => $movie->type])->select();
        $genres = Genres::whereIn('hash', $movie->genres)->where(['type' => $movie->type])->select();
        $publisher = Publishers::where(['hash' => $movie->publisher])->find();
        $producer = Producers::where(['hash' => $movie->producers])->find();
        $director = Directors::where(['hash' => $movie->director])->find();

        return View::fetch('index@detail', compact('movie', 'magnetics', 'similars', 'stars', 'genres', 'publisher', 'producer', 'director'));
    }
}
