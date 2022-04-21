<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ForumComments extends Migrator
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
        $table = $this->table('forum_comments', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('user', 'string', ['limit' => 50, 'comment' => '发布人'])
            ->addColumn('content', 'text', ['comment' => '内容'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'comment' => '头像'])
            ->addColumn('post_id', 'integer', ['limit' => 11, 'comment' => '评论贴'])
            ->create();
    }
}
