<?php

use Phinx\Migration\AbstractMigration;

class CreateUserGroupsTable extends AbstractMigration
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
        $userGroups = $this->table('user_groups', ['id' => false, 'primary_key' => ['user_id', 'group_id']]);
        $userGroups->addColumn('user_id',  'integer', ['signed' => false])
                   ->addColumn('group_id', 'integer', ['signed' => false])

                   ->addForeignKey('user_id',  'users',  'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                   ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])

                   ->create();
    }
}
