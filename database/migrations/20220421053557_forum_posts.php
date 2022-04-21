<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ForumPosts extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('forum_posts', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('title', 'string', ['limit' => 255, 'comment' => '标题'])
            ->addColumn('content', 'text', ['comment' => '内容'])
            ->addColumn('user', 'string', ['limit' => 50, 'comment' => '发布人'])
            ->addColumn('views', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '浏览数'])
            ->addColumn('collects', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '收藏数'])
            ->addColumn('thumbs', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '点赞数'])
            ->addColumn('replies', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '回复数'])
            ->addColumn('topic_id', 'integer', ['limit' => 11, 'comment' => '所属主题'])
            ->addColumn('publish_time', 'datetime', ['comment' => '发布时间'])
            ->create();
    }
}
