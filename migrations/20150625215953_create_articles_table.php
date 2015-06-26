<?php

use Phinx\Migration\AbstractMigration;

class CreateArticlesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $articles = $this->table('articles');
        $articles->addColumn('title',     'string', ['limit' => 64])
                 ->addColumn('feed_id',   'integer')
                 ->addColumn('body',      'text')
                 ->addColumn('uri',       'string', ['limit' => 256])
                 ->addColumn('published', 'datetime')

                 ->addIndex(['title', 'uri', 'published'])

                 ->addForeignKey('feed_id', 'feeds', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                 ->create();
    }
}
