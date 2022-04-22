<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Movies extends Migrator
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
        $table = $this->table('movies', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('title', 'string', ['limit' => 255, 'comment' => '名称'])
            ->addColumn('hash', 'string', ['limit' => 30, 'default' => '', 'comment' => '标识'])
            ->addColumn('sdde', 'string', ['limit' => 50, 'default' => '', 'comment' => '番号'])
            ->addColumn('cover', 'string', ['limit' => 255, 'default' => '', 'comment' => '封面'])
            ->addColumn('stars', 'string', ['limit' => 255, 'default' => '', 'comment' => '主演列表'])
            ->addColumn('genres', 'string', ['limit' => 255, 'default' => '', 'comment' => '分类列表'])
            ->addColumn('series', 'string', ['limit' => 10, 'default' => '', 'comment' => '系列列表'])
            ->addColumn('publisher', 'string', ['limit' => 10, 'default' => '', 'comment' => '发行商'])
            ->addColumn('producer', 'string', ['limit' => 10, 'default' => '', 'comment' => '制片商'])
            ->addColumn('director', 'string', ['limit' => 10, 'default' => '', 'comment' => '导演'])
            ->addColumn('similars', 'string', ['limit' => 255, 'default' => '', 'comment' => '同类型'])
            ->addColumn('duration', 'integer', ['limit' => 5, 'default' => 0, 'comment' => '时长'])
            ->addColumn('is_hd', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否高清，「0非高清，1高清」'])
            ->addColumn('is_subtitle', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '有无字幕，「0无字幕，1有字幕」'])
            ->addColumn('views', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '浏览数'])
            ->addColumn('collects', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '收藏数'])
            ->addColumn('previews', 'integer', ['limit' => 3, 'default' => 0, 'comment' => '预览图数'])
            ->addColumn('score', 'float', ['default' => 0, 'comment' => '评分'])
            ->addColumn('type', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '类型，「0有码，1无码」'])
            ->addColumn('publish_date', 'date', ['comment' => '发布日期'])
            ->create();
    }
}
