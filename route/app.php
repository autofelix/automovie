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
Route::get('/director', '\app\index\controller\Index@director');
Route::get('/publisher', '\app\index\controller\Index@publisher');
Route::get('/producer', '\app\index\controller\Index@producer');
Route::get('/forum', '\app\index\controller\Forum@index');
Route::get('/search', '\app\index\controller\Index@search');
Route::get('/detail/:sdde', '\app\index\controller\Detail@index')->pattern(['sdde' => '[\w-]+']);

Route::group('/sort', function () {
    Route::get('/popular', 'app\index\controller\Sort@popular');
    Route::get('/sowar', 'app\index\controller\Sort@sowar');
    Route::get('/infantry', 'app\index\controller\Sort@infantry');
    Route::get('/magnetic', 'app\index\controller\Sort@magnetic');
    Route::get('/hd', 'app\index\controller\Sort@hd');
    Route::get('/subtitle', 'app\index\controller\Sort@subtitle');
});

Route::group('/star', function () {
    Route::get('/sowar', 'app\index\controller\Star@sowar');
    Route::get('/infantry', 'app\index\controller\Star@infantry');
    Route::get('/sowar/:hash', 'app\index\controller\Star@sowar_profile');
    Route::get('/infantry/:hash', 'app\index\controller\Star@infantry_profile');
    Route::get('/sowar/alone/:hash', 'app\index\controller\Star@sowar_alone_profile');
    Route::get('/infantry/alone/:hash', 'app\index\controller\Star@infantry_alone_profile');
});
