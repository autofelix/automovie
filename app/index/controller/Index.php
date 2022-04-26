<?php
declare (strict_types=1);

namespace app\index\controller;

use think\facade\View;
use think\Request;

use app\model\Movies;
use app\model\Stars;


class Index
{
    public function index()
    {
        $movies = Movies::order('publish_date', 'desc')->paginate(24);

        return View::fetch('/index', compact('movies'));
    }

    public function search(Request $request)
    {
        $keywords = $request->param('keywords');

        $movies = Movies::whereLike('title', "%{$keywords}%")
            ->paginate([
                'list_rows' => 24,
                'query' => $request->param()
            ]);

        return View::fetch('index@index', compact('movies'));
    }

    public function director()
    {
        $stars = Stars::where(['type' => 0])->paginate(60);
        return View::fetch('index@director', compact('stars'));
    }

    public function publisher()
    {
        $stars = Stars::where(['type' => 0])->paginate(60);
        return View::fetch('index@publisher', compact('stars'));
    }

    public function producer()
    {
        $stars = Stars::where(['type' => 0])->paginate(60);
        return View::fetch('index@producer', compact('stars'));
    }
}
