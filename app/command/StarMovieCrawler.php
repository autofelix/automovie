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
    private $spider_map = [
        0 => 'https://www.seedmm.fun/star/',
        1 => 'https://www.seedmm.fun/uncensored/star/',
    ];

    private $headers = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
        'Referer' => 'https://www.seedmm.fun',
        'Cookie' => 'existmag=all',
    ];


    protected function configure()
    {
        // 指令配置
        $this->setName('star_movie_crawler')
            ->addArgument('hash', Argument::REQUIRED, "star hash")
            ->addArgument('type', Argument::REQUIRED, "star type")
            ->setDescription('the star movie crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $star_hash = trim($input->getArgument('hash'));
        $type = trim($input->getArgument('type'));

        $url = $this->spider_map[$type] . $star_hash;

        $ql = QueryList::get($url, null, [
            'headers' => $this->headers
        ]);

        $pages = $ql->find('ul.pagination>li>a')->attrs('href')->all();

        array_walk($pages, function (&$item) {
            $page = explode('/', $item);
            $item = end($page);
        });

        # 获取总共多少页数
        $total = $pages ? max($pages) : 1;

        // 爬取所有页面的数据
        $urls = [];
        for ($i = 1; $i <= $total; $i++) {
            $urls[] = $url . '/' . $i;
        }

        $rules = [
            'info' => ['date', 'texts'],
            'title' => ['img', 'attr(title)', '', function ($title) {
                return trim($title);
            }],
            'is_hd' => ['button.btn.btn-xs.btn-primary', 'text'],
            'is_subtitle' => ['button.btn.btn-xs.btn-warning', 'text'],
            'href' => ['.movie-box', 'href']
        ];

        $range = '#waterfall>.item';

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
            ->withHeaders($this->headers)
            // HTTP success回调函数
            ->success(function (QueryList $ql, Response $response, $index) use ($type, $star_hash) {
                $result = $ql->queryData();

                foreach ($result as $item) {
                    if ($item['info'] && $item['title'] && $item['href']) {
                        //去除重复影片数据
                        $is_exists = Db::name('movies')->where([
                            'sdde' => $item['info'][0],
                            'type' => $type,
                        ])->whereLike('stars', "%{$star_hash}%")->find();

                        if (!$is_exists) {
                            $movie = QueryList::get($item['href'], null, [
                                'headers' => $this->headers
                            ]);

                            $title = $movie->find('h3')->text();
                            $cover = $movie->find('a.bigImage')->attr('href');
                            $mark = $movie->find('body>script:eq(2)')->text();
                            $is_hd = $item['is_hd'] ? 1 : 0;
                            $is_subtitle = $item['is_subtitle'] ? 1 : 0;
                            $info = $movie->find('div.col-md-3.info')->html();

                            preg_match('#var gid = (\d+);#', $mark, $out_hash);
                            $hash = $out_hash[1];

                            preg_match('#var uc = (\d+);#', $mark, $out_uc);
                            $uc = $out_uc[1];


                            $insert_movie_data = [
                                'title' => $title,
                                'cover' => $cover,
                                'is_hd' => $is_hd,
                                'is_subtitle' => $is_subtitle,
                            ];

                            halt($insert_movie_data);

                            $insert_magnet_data = [];
                            if ($hash) {
                                $magnet_url = "https://www.seedmm.fun/ajax/uncledatoolsbyajax.php?gid={$hash}&lang=zh&uc={$uc}";
                                //获取磁力
                                $magnet = QueryList::get($magnet_url, null, [
                                    'headers' => $this->headers
                                ])
                                    ->rules([
                                        'name' => ['td:eq(0)>a', 'texts', '', function ($title) {
//                                            return trim($title);
                                        }],
                                        'magnet' => ['td:eq(0)>a', 'hrefs', '', function ($magnet) {
//                                            preg_match('#magnet:?xt=urn:btih:(.*?)&dn#', $magnet, $out);
//                                            return trim($out[1]);
                                        }],
                                        'is_hd' => ['a.btn.btn-mini-new.btn-primary', 'html', '', function ($is_hd) {
//                                            return $is_hd ? 1 : 0;
                                        }],
                                        'is_subtitle' => ['a.btn.btn-mini-new.btn-warning', 'html', '', function ($is_subtitle) {
//                                            return $is_subtitle ? 1 : 0;
                                        }],
                                        'size' => ['td:eq(1)>a', 'texts', '', function ($size) {
//                                            return trim($size);
                                        }],
                                        'publish_date' => ['td:eq(2)>a', 'texts', '', function ($publish_date) {
//                                            return trim($publish_date);
                                        }]
                                    ])
                                    ->range('tr')
                                    ->queryData(function ($item, $key) use ($hash) {
                                        $item['is_magnetic'] = 1;
                                        $item['hash'] = $hash;
                                        return $item;
                                    });

                                halt($magnet);
                            }

                            halt('防止入库了');

                            Db::transaction(function () use ($insert_movie_data, $insert_magnet_data) {
                                if ($insert_movie_data) Db::name('stars')->insert($insert_movie_data);
                                if ($insert_magnet_data) Db::name('magnetics')->insertAll($insert_magnet_data);
                            });
                        }

                    }
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
