<?php
declare (strict_types=1);

namespace app\command;

use GuzzleHttp\Psr7\Response;
use QL\QueryList;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class ForumTopicCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('forum_topic_crawler')
            ->setDescription('the forum topic crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $url = '';

        $headers = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
                'Accept-Encoding' => 'gzip, deflate, br',
            ]
        ];

        # 父主题
        $topic = QueryList::get($url, null, $headers)
            ->rules([
                'name' => ['h3', 'texts'],
                'cover' => ['h3', 'texts'],
                'desc' => ['h3', 'texts'],
            ])->queryData(function ($item) {
                $item['p_id'] = 0;
                return $item;
            });

        # 子主题
        $sub_topic = QueryList::get($url, null, $headers)
            ->rules([
                'name' => ['h3', 'texts'],
                'cover' => ['h3', 'texts'],
                'desc' => ['h3', 'texts'],
            ])->queryData(function ($item) {
                $item['p_id'] = 1;
                return $item;
            });

        Db::name('forum_topics')->insertAll($topic);
        Db::name('forum_topics')->insertAll($sub_topic);

        // 指令输出
        $output->writeln('=====================  论坛采集成功 =======================');
    }
}
