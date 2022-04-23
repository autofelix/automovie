<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::group('/star', function () {
    Route::get('/', 'app\index\controller\Star@list');
    Route::get('/infantry', 'app\index\controller\Star@infantry');
    Route::get('/alone/:hash', 'app\index\controller\Star@alone');
    Route::get('/profile/:hash', 'app\index\controller\Star@profile');
});
