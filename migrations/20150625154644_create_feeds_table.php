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
        $feeds->addColumn('name',            'string',   ['limit'   => 64])
              ->addColumn('website_uri',     'string',   ['limit'   => 255])
              ->addColumn('feed_uri',        'string',   ['limit'   => 255])
              ->addColumn('description',     'string',   ['limit'   => 255, 'null' => true])
              ->addColumn('added',           'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated',         'datetime', ['update'  => 'CURRENT_TIMESTAMP',
                                                          'default' => '1970-01-01 00:00:00'])
              ->addColumn('update_interval', 'integer',  ['signed'  => false])
              ->addColumn('user_id',         'integer',  ['signed'  => false])

              ->addIndex(['name', 'updated', 'update_interval', 'user_id'])

              ->create();
    }
}
