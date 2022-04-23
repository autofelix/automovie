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

Route::get('/', '\app\index\controller\Index@index');
Route::get('/detail/:sdde', '\app\index\controller\Detail@index')->pattern(['sdde' => '[\w-]+']);


Route::group('/star', function () {
    Route::get('/', 'app\index\controller\Star@list');
    Route::get('/infantry', 'app\index\controller\Star@infantry');
});

Route::group('/profile', function () {
    Route::get('/alone/:hash', 'app\index\controller\Star@alone');
    Route::get('/sowar/:hash', 'app\index\controller\Star@sowar_profile');
    Route::get('/infantry/:hash', 'app\index\controller\Star@infantry_profile');
});
