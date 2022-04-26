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
use think\facade\Console;

class ForumPostCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('forum_post_crawler')
            ->addArgument('url', Argument::REQUIRED, "forum first url")
            ->addArgument('total', Argument::REQUIRED, "total page")
            ->addArgument('topic_id', Argument::REQUIRED, "topic id")
            ->setDescription('the forum post crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $url = trim($input->getArgument('url'));
        $total = trim($input->getArgument('total'));
        $topic_id = trim($input->getArgument('topic_id'));

        $urls = [];
        for ($i = 1; $i <= $total; $i++) {
            $urls[] = str_replace('page/1', "page/{$i}", $url);
        }

        $rules = [
            'link' => ['.message', 'href']
        ];

        $range = '.repo-list>li';

        QueryList::rules($rules)
            ->range($range)
            ->multiGet($urls)
            // 设置并发数为2
            ->concurrency(2)
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
            ->success(function (QueryList $ql, Response $response, $index) use ($topic_id) {

                $result = QueryList::get($ql->queryData()['href'])->rules([
                    'title' => ['.name', 'text'],
                    'content' => ['.name', 'html'],
                    'user' => ['.name', 'text'],
                    'views' => ['.name', 'text'],
                    'collects' => ['.name', 'text'],
                    'thumbs' => ['.name', 'text'],
                    'publish_time' => ['.name', 'text'],
                    'total' => [],
                    'url' => []
                ])->query()->getData(function ($item, $key) use ($topic_id) {
                    $item['topic_id'] = $topic_id;
                    return $item;
                });

                $post_id = Db::name('forum_posts')->insert($result->queryData());

                $url = $result->queryData()['total'];
                $total = $result->queryData()['url'];

                // 获取评论
                Console::call('forum_comment', [$url, $post_id, $total]);
            })
            // HTTP error回调函数
            ->error(function (QueryList $ql, $reason, $index) {
                // ...
                var_dump($ql);
            })
            ->send();

        // 指令输出
        $output->writeln('=====================  论坛文章采集成功 =======================');
    }
}
