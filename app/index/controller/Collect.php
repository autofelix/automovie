<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Request;
use think\facade\Console;

class Collect
{
    public function star(Request $request)
    {
        $data = $request->only(['hash', 'type']);
        $hash = $data['hash'];
        $type = $data['type'];

        $output = Console::call('spider:star_movie', [$hash, $type]);

        return Json([
            'status' => 200,
            'message' => '采集成功',
            'output' => $output->fetch()
        ]);
    }
}
