<?php
declare (strict_types=1);

namespace app\index\controller;

use think\facade\View;
use think\Request;

use app\model\Movies;

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
}
