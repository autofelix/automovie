<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Magnetics extends Migrator
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
        $table = $this->table('magnetics', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('magnet', 'string', ['limit' => 100, 'comment' => '地址，magnet:?xt=urn:btih:'])
            ->addColumn('name', 'string', ['limit' => 255, 'comment' => '名称'])
            ->addColumn('hash', 'string', ['limit' => 30, 'default' => '', 'comment' => '标识'])
            ->addColumn('is_hd', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否高清，「0非高清，1高清」'])
            ->addColumn('is_subtitle', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '有无字幕，「0无字幕，1有字幕」'])
            ->addColumn('size', 'string', ['limit' => 10, 'default' => '', 'comment' => '大小'])
            ->addColumn('publish_date', 'date', ['comment' => '发布时间'])
            ->create();
    }
}
