<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'spider:star' => 'app\command\StarCrawler',
        'spider:movie' => 'app\command\MovieCrawler',
        'spider:genre' => 'app\command\GenreCrawler',
        'spider:star_movie' => 'app\command\StarMovieCrawler',
        'spider:forum_topic' => 'app\command\ForumTopicCrawler',
        'spider:forum_post' => 'app\command\ForumPostCrawler',
        'spider:forum_comment' => 'app\command\ForumCommentCrawler'
    ],
];
