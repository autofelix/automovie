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
use think\facade\Console;
use think\facade\Db;

class ForumCommentCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('forum comment crawler')
            ->addArgument('url', Argument::REQUIRED, "comment first url")
            ->addArgument('post_id', Argument::REQUIRED, "topic id")
            ->addArgument('total', Argument::REQUIRED, "total page")
            ->setDescription('the forum_comment_crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $url = trim($input->getArgument('url'));
        $total = trim($input->getArgument('total'));
        $post_id = trim($input->getArgument('post_id'));

        $urls = [];
        for ($i = 1; $i <= $total; $i++) {
            $urls[] = str_replace('page/1', "page/{$i}", $url);
        }

        $rules = [
            'user' => ['.message', 'href'],
            'content' => ['.message', 'html'],
            'avatar' => ['.message', 'href']
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
            ->success(function (QueryList $ql, Response $response, $index) use ($post_id) {

                $ql->queryData()['post_id'] = $post_id;

                Db::name('forum_comments')->insert($ql->queryData());
            })
            // HTTP error回调函数
            ->error(function (QueryList $ql, $reason, $index) {
                // ...
                var_dump($ql);
            })
            ->send();

        // 指令输出
        $output->writeln('=====================  文章评论采集成功 =======================');
    }
}
