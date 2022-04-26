<?php
declare (strict_types=1);

namespace app\command;

use think\facade\Db;
use GuzzleHttp\Psr7\Response;
use QL\QueryList;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class GenreCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('genre_crawler')
            ->setDescription('the genre crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $urls = [
            'https://www.seedmm.fun/genre',
            'https://www.seedmm.fun/uncensored/genre'
        ];

        $rules = [
            'name' => ['', 'text', '', function ($name) {
                return trim($name);
            }],
            'hash' => ['', 'href', '', function ($hash) {
                $parts = explode('/', $hash);
                return end($parts);
            }]
        ];

        $range = 'a.col-lg-2.col-md-2.col-sm-3.col-xs-6.text-center';

        QueryList::rules($rules)
            ->range($range)
            ->multiGet($urls)
            // 设置并发数为2
            ->concurrency(2)
            // 设置GuzzleHttp的一些其他选项
            ->withOptions([
                'timeout' => 60,
                'verify' => false,
            ])
            // 设置HTTP Header
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
                'Referer' => 'https://www.seedmm.fun',
            ])
            // HTTP success回调函数
            ->success(function (QueryList $ql, Response $response, $index) {
                // $data = $ql->queryData();
                $data = $ql->query(function ($item) use ($index) {
                    $item['type'] = $index == 0 ? 0 : 1;
                    $item['time'] = date('Y-m-d H:i:s');
                    return $item;
                })->getData();

                Db::name('genres')->insertAll($data->all());
            })
            // HTTP error回调函数
            ->error(function (QueryList $ql, $reason, $index) {
                // ...
                var_dump($ql);
            })
            ->send();

        // 指令输出
        $output->writeln('=====================  分类采集成功 =======================');
    }
}
