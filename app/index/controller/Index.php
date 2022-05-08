<?php
declare (strict_types=1);

namespace app\index\controller;

use think\facade\View;
use think\Request;

use app\model\Movies;
use app\model\Stars;
use app\model\Producers;
use app\model\Publishers;
use app\model\Directors;


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
            ->order('publish_date', 'desc')
            ->paginate([
                'list_rows' => 24,
                'query' => $request->param()
            ]);

        return View::fetch('index@index', compact('movies'));
    }

    public function director()
    {
        $directors = Directors::paginate(60);
        return View::fetch('index@director', compact('directors'));
    }

    public function publisher()
    {
        $publishers = Publishers::paginate(60);
        return View::fetch('index@publisher', compact('publishers'));
    }

    public function producer()
    {
        $producers = Producers::paginate(60);
        return View::fetch('index@producer', compact('producers'));
    }
}
