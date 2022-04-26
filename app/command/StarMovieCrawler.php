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

class StarMovieCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('star_movie_crawler')
            ->addArgument('url', Argument::REQUIRED, "star home url")
            ->addArgument('type', Argument::REQUIRED, "type")
            ->setDescription('the star movie crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $url = trim($input->getArgument('url'));
        $type = trim($input->getArgument('type'));

        // 定义采集规则
        $rules = [
            // 采集总页数
            'total' => ['h1', 'text'],
            // 采集最后一页的链接
            'href' => ['.last_page>a', 'href']
        ];

        $response = QueryList::get($url)->rules($rules)->query()->getData();

        $total = $response->all()['total'];
        $href = $response->all()['href'];

        // 爬取所有页面的数据
        $urls = [];
        for ($i = 1; $i <= $total; $i++) {
            $urls[] = str_replace('page/1', "page/{$i}", $href);
        }

        $rules = [
            'star' => ['.star.name', 'text', '', function ($star) {
                return trim($star);
            }],
            'title' => ['.star.movie', 'text', '', function ($title) {
                return trim($title);
            }],
            'href' => ['.star.movie', 'href']
        ];

        $range = '.repo-list>li';

        QueryList::rules($rules)
            ->range($range)
            ->multiGet($urls)
            // 设置并发数为20
            ->concurrency(20)
            // 设置GuzzleHttp的一些其他选项
            ->withOptions([
                'timeout' => 60,
                'verify' => false,
            ])
            // 设置HTTP Header
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            // HTTP success回调函数
            ->success(function (QueryList $ql, Response $response, $index) use ($type) {
                //去除重复影片数据
                $is_exists = Db::name('movies')->where([
                    'title' => $ql->queryData()['title'],
                    'type' => $type,
                ])->whereLike('stars', "%{$ql->queryData()['star']}%")->find();

                if (!$is_exists) {
                    //每获取一个url，则获取详情数据
                    $movie = QueryList::get($ql->queryData()['href'])->rules([
                        'name' => ['.name', 'text'],
                        'hash' => ['.name', 'text'],
                        'avatar' => ['.name', 'text'],
                        'birthday' => ['.name', 'text'],
                        'hometown' => ['.name', 'text'],
                        'age' => ['.name', 'text'],
                        'height' => ['.name', 'text'],
                        'cupsize' => ['.name', 'text'],
                        'bust' => ['.name', 'text'],
                        'waist' => ['.name', 'text'],
                        'hip' => ['.name', 'text'],
                        'hobby' => ['.name', 'text']
                    ])->query()->getData(function ($item, $key) use ($type) {
                        $item['type'] = $type;
                        return $item;
                    });

                    $hash = $movie->queryData()['hash'];
                    //获取磁力
                    $magnet = QueryList::get($ql->queryData()['href'])
                        ->rules([
                            'name' => ['.name', 'texts', '', function ($title) {
                                return trim($title);
                            }],
                            'magnet' => ['.name', 'texts'],
                            'is_hd' => ['.name', 'texts', '', function ($title) {
                                return strstr($title, '高清') === false ? 0 : 1;
                            }],
                            'is_subtitle' => ['.name', 'texts', '', function ($title) {
                                return strstr($title, '字幕') === false ? 0 : 1;
                            }],
                            'size' => ['.name', 'texts'],
                            'publish_date' => ['.name', 'date']
                        ])
                        ->range('tr')
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
                            'Referer' => '',
                        ])
                        ->query()
                        ->getData(function ($item, $key) use ($hash) {
                            $item['hash'] = $hash;
                            return $item;
                        });

                    Db::name('stars')->insert($movie->queryData());
                    Db::name('magnetics')->insertAll($magnet->queryData());
                }
            })
            // HTTP error回调函数
            ->error(function (QueryList $ql, $reason, $index) {
                // ...
                var_dump($ql);
            })
            ->send();

        // 指令输出
        $output->writeln('=====================  演员影片采集成功 =======================');
    }
}
