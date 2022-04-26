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

class StarCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
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
            $urls[] = str_replace('page/1', "page/{$i}", $url);
        }

        $rules = [
            'name' => ['.name', 'text'],
            'avatar' => ['.avatar', 'text'],
            'link' => ['.link>a', 'href', '', function ($href) {
                return 'http:' . $href;
            }]
        ];

        $range = '.item';

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
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            // HTTP success回调函数
            ->success(function (QueryList $ql, Response $response, $index) use ($type) {
                // $data = $ql->queryData();

                $result = QueryList::get($ql->queryData()['href'])->rules([
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

//                print_r($response->queryData());

                Db::name('stars')->insert($result->queryData());
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
