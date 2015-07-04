<?php

use Phinx\Migration\AbstractMigration;

class AddUsersGroup extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
    public function change()
    {
    }
    */

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO groups (id, name, system) VALUES (1, 'users', true)");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
