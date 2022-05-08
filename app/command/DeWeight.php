<?php
declare (strict_types = 1);

namespace app\command;

use think\Db;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class DeWeight extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\deweight')
            ->setDescription('the app\command\deweight command');
    }

    protected function execute(Input $input, Output $output)
    {
        $movies = Db::query("
            SELECT title,stars,is_magnetic,type FROM movies WHERE cover IN (
                SELECT cover FROM movies GROUP BY cover HAVING COUNT(1)>1
            ) 
            AND id NOT IN (
                SELECT MIN(id) FROM movies GROUP BY cover HAVING COUNT(1)>1
            ) ORDER BY title
        ");

        $count = count($movies);
        halt($movies);
        $step = 0;

        foreach ($movies as $star) {
            $step += 1;
            try {
                $output = Console::call('spider:star_movie', [$star['hash'], (string)$star['type']]);

                $output->writeln($output->fetch());
            } catch (\Throwable $e) {
                var_dump($e->getMessage());
            }

            var_dump("----正在执行{$step}/{$count}个任务-----");
            // 指令输出
            $output->writeln('app\command\deweight');
        }
    }
}
