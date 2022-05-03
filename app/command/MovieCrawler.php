<?php
declare (strict_types=1);

namespace app\command;

use Think\Exception;
use think\facade\Db;
use GuzzleHttp\Psr7\Response;
use QL\QueryList;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\model\Stars;
use think\facade\Console;

class MovieCrawler extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('movie_crawler')
            ->setDescription('the movie crawler command');
    }

    protected function execute(Input $input, Output $output)
    {
        $stars = Stars::where([
            'type' => 0
        ])
            ->where('id', '>=', 19954)
            ->field('hash,type')
            ->select()
            ->toArray();

        $count = count($stars);
        $step = 0;

        foreach ($stars as $star) {
            $step += 1;
            try {
                $output = Console::call('spider:star_movie', [$star['hash'], (string)$star['type']]);

                $output->writeln($output->fetch());
            } catch (\Throwable $e) {
                var_dump($e->getMessage());
            }

            var_dump("----正在执行{$step}/{$count}个任务-----");
        }
    }
}
