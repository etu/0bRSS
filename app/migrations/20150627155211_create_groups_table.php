<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupsTable extends AbstractMigration
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
        $groups = $this->table('groups', ['signed' => false]);
        $groups->addColumn('name',  'string', ['limit' => 64])
               ->addColumn('system', 'boolean')

               ->addIndex(['name'], ['unique' => true])

               ->create();
    }
}
