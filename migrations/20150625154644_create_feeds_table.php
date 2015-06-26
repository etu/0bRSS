<?php

use Phinx\Migration\AbstractMigration;

class CreateFeedsTable extends AbstractMigration
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
        $feeds = $this->table('feeds');
        $feeds->addColumn('name',            'string',   ['limit' => 64])
              ->addColumn('websiteUri',      'string',   ['limit' => 256])
              ->addColumn('feedUri',         'string',   ['limit' => 256])
              ->addColumn('added',           'datetime')
              ->addColumn('updated',         'datetime', ['null' => true])
              ->addColumn('update_interval', 'integer')

              ->addIndex(['name', 'updated', 'update_interval'])
              ->create();
    }
}
