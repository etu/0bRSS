<?php

use Phinx\Migration\AbstractMigration;

class CreateUserApiTokensTable extends AbstractMigration
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
        $users = $this->table('user_api_tokens', ['signed' => false]);
        $users->addColumn('token',      'string',   ['limit'   => 64])
              ->addColumn('user_id',    'integer',  ['signed'  => false])
              ->addColumn('created',    'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('expires',    'datetime', ['update'  => 'CURRENT_TIMESTAMP', 'null' => true])

              ->addIndex(['token'], ['unique' => true])

              ->addForeignKey('user_id',  'users',  'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])

              ->create();
    }
}
