<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
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
        $users = $this->table('users', ['signed' => false]);
        $users->addColumn('email',      'string',   ['limit'   => 255])
              ->addColumn('password',   'string',   ['limit'   => 255])
              ->addColumn('name',       'string',   ['limit'   => 255])
              ->addColumn('created',    'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated',    'datetime', ['update'  => 'CURRENT_TIMESTAMP', 'null' => true])
              ->addColumn('last_login', 'datetime', ['update'  => 'CURRENT_TIMESTAMP', 'null' => true])

              ->addIndex(['email'],  ['unique' => true])
              ->addIndex(['password', 'name'])

              ->create();
    }
}
