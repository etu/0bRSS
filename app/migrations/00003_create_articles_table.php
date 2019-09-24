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
        $articles = $this->table('articles', ['signed' => false]);
        $articles->addColumn('feed_id',    'integer', ['signed' => false])
                 ->addColumn('identifier', 'string',  ['limit'  => 32])
                 ->addColumn('title',      'string',  ['limit'  => 255])
                 ->addColumn('uri',        'string',  ['limit'  => 255])
                 ->addColumn('date',       'datetime')
                 ->addColumn('body',       'text')
                 ->addColumn('read',       'boolean', ['default' => false])
                 ->addColumn('starred',    'boolean', ['default' => false])

                 ->addIndex(['title', 'uri', 'date'])
                 ->addIndex(['feed_id', 'identifier'], ['unique' => true])

                 ->addForeignKey('feed_id', 'feeds', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])

                 ->create();
    }
}
