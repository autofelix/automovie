<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Request;

use think\facade\View;

use app\index\model\Stars;

class Star
{
    public function list()
    {

    }

    public function profile(Request $request, $hash)
    {
        $star = Stars::where(['hash' => $hash])->find();

        return View::fetch('profile', compact('star'));
    }
}
