<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Stars extends Migrator
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
        $table = $this->table('stars', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('name', 'string', ['limit' => 100, 'comment' => '姓名'])
            ->addColumn('hash', 'string', ['limit' => 10, 'comment' => '标识'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '头像'])
            ->addColumn('birthday', 'date', ['limit' => 10, 'comment' => '生日'])
            ->addColumn('hometown', 'string', ['limit' => 50, 'default' => '', 'comment' => '家乡'])
            ->addColumn('age', 'integer', ['limit' => 3, 'default' => 0, 'comment' => '年龄'])
            ->addColumn('height', 'integer', ['limit' => 3, 'default' => 0, 'comment' => '身高'])
            ->addColumn('cupsize', 'string', ['limit' => 3, 'default' => '', 'comment' => '罩杯'])
            ->addColumn('bust', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '胸围'])
            ->addColumn('waist', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '腰围'])
            ->addColumn('hip', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '臀围'])
            ->addColumn('hobby', 'string', ['limit' => 255, 'default' => '', 'comment' => '爱好'])
            ->addColumn('type', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '类型，「0有码，1无码」'])
            ->addColumn('time', 'datetime', ['comment' => '抓取时间'])
            ->create();
    }
}
