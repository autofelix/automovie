<?php
declare (strict_types=1);

namespace app\command;

use think\Exception;
use think\facade\Db;
use GuzzleHttp\Psr7\Response;
use QL\QueryList;
use QL\Ext\CurlMulti;
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

        $ql = QueryList::getInstance();
        $ql->use(CurlMulti::class);

        $ql->rules($rules)
            ->range($range)
            ->curlMulti($urls)
            // HTTP success回调函数
            ->success(function (QueryList $ql, CurlMulti $curl, $r) use ($type) {
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
                            if (isset($out[1])) $item[$k] = $out[1];
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

                QueryList::destructDocuments();

            })
            // HTTP error回调函数
            ->error(function (QueryList $ql, $reason, $index) {
                // ...
                var_dump($ql);
            })
            ->start([
                // 最大并发数，这个值可以运行中动态改变。
                'maxThread' => 30,
                // 触发curl错误或用户错误之前最大重试次数，超过次数$error指定的回调会被调用。
                'maxTry' => 10,
                // 全局CURLOPT_*
                'opt' => [
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 1,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => [
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
                        'Referer' => 'https://www.seedmm.fun',
                    ]
                ],
            ]);

        // 指令输出
        $output->writeln('=====================  演员采集成功 =======================');
    }
}
