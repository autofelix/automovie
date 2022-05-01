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
        $stars = Stars::where(['type' => 1])->field('hash,type')->select()->toArray();

        foreach ($stars as $star) {
            try{
                $output = Console::call('spider:star_movie', [$star['hash'], (string)$star['type']]);

                $output->writeln($output->fetch());
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
        }
    }
}
