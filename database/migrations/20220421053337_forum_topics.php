<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ForumTopics extends Migrator
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
        $table = $this->table('forum_topics', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '名称'])
            ->addColumn('cover', 'string', ['limit' => 255, 'default' => '', 'comment' => '封面，「https://www.seedmm.fun/forum/data/attachment/common/」'])
            ->addColumn('desc', 'string', ['limit' => 255, 'default' => '', 'comment' => '描述'])
            ->create();
    }
}
