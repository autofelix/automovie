<?php
declare (strict_types=1);

namespace app\command;

use think\Exception;
use think\facade\Db;
use GuzzleHttp\Psr7\Response;
use QL\QueryList;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class StarCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        /**
         * 每页50张演员图
         * https://www.seedmm.fun/actresses/1    905   0
         * https://www.seedmm.fun/uncensored/actresses/1 436  1
         */
        $this->setName('star_crawler')
            ->addArgument('url', Argument::REQUIRED, "first url")
            ->addArgument('total', Argument::REQUIRED, "total page")
            ->addArgument('type', Argument::REQUIRED, "star type")
            ->setDescription('the star crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $url = trim($input->getArgument('url'));
        $total = trim($input->getArgument('total'));
        $type = trim($input->getArgument('type'));

        $urls = [];
        for ($i = 1; $i <= $total; $i++) {
            $urls[] = str_replace('1', "{$i}", $url);
        }

        $rules = [
            'hash' => ['', 'href', '', function ($hash) {
                $parts = explode('/', $hash);
                return end($parts);
            }],
            'link' => ['', 'href']
        ];

        $range = 'a.avatar-box';

        QueryList::rules($rules)
            ->range($range)
            ->multiGet($urls)
            // 设置并发数为2
            ->concurrency(20)
            // 设置GuzzleHttp的一些其他选项
            ->withOptions([
                'timeout' => 60,
                'verify' => false
            ])
            // 设置HTTP Header
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
                'Referer' => 'https://www.seedmm.fun',
            ])
            // HTTP success回调函数
            ->success(function (QueryList $ql, Response $response, $index) use ($type) {
                $item = $ql->queryData();
                $hash = $item[0]['hash'];

                $result = QueryList::get($item[0]['link'])->rules([
                    'name' => ['div.avatar-box>.photo-info>.pb10', 'text', '', function ($name) {
                        return trim($name);
                    }],
                    'avatar' => ['div.avatar-box>.photo-frame>img', 'src', '', function ($avatar) {
                        if ($avatar) {
                            $parts = explode('/', $avatar);
                            return end($parts);
                        } else {
                            return 'nowprinting.gif';
                        }
                    }],
                    'info' => ['div.avatar-box>.photo-info', 'text', 'p -span']
                ])->query()->getData(function ($item, $key) use ($type, $hash) {
                    $item['type'] = $type;
                    $item['hash'] = $hash;
                    $item['birthday'] = '1970-01-01';
                    $item['time'] = date('Y-m-d H:i:s');

                    if ($item['info']) {
                        $map = [
                            'birthday' => '#<p>生日: (.*?)</p>#',
                            'age' => '#<p>年齡: (.*?)</p>#',
                            'height' => '#<p>身高: (.*?)cm</p>#',
                            'cupsize' => '#<p>罩杯: (.*?)</p>#',
                            'bust' => '#<p>胸圍: (.*?)cm</p>#',
                            'waist' => '#<p>腰圍: (.*?)cm</p>#',
                            'hip' => '#<p>臀圍: (.*?)cm</p>#',
                            'hometown' => '#<p>出生地: (.*?)</p>#',
                            'hobby' => '#<p>愛好: (.*?)</p>#',
                        ];

                        foreach ($map as $k => $v) {
                            preg_match($v, $item['info'], $out);
                            if ($out[1]) $item[$k] = $out[1];
                        }
                    }

                    unset($item['info']);
                    return $item;
                });

                try {
                    Db::name('stars')->insert($result->all());
                } catch (Exception $e) {
                    print_r($e->getMessage());
                }

            })
            // HTTP error回调函数
            ->error(function (QueryList $ql, $reason, $index) {
                // ...
                var_dump($ql);
            })
            ->send();

        // 指令输出
        $output->writeln('=====================  演员采集成功 =======================');
    }
}
