<?php
declare(strict_types=1);

namespace ZerobRSS\Dao;

use Doctrine\DBAL\Connection as Db;
use PDOStatement;

class UserGroups
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getUserGroups(string $value, string $column = 'user_id') : PDOStatement
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('user_groups', 'ug')
            ->innerJoin('ug', 'groups', 'g', 'g.id = ug.group_id')
            ->where($column.' = :value')
            ->setParameter(':value', $value)
            ->execute();
    }

    public function addUserToGroup(int $userId, int $groupId) : bool
    {
        return (bool) $this->db->createQueryBuilder()
            ->insert('user_groups')
            ->setValue('user_id', ':user_id')
            ->setValue('group_id', ':group_id')
            ->setParameter(':user_id', $userId)
            ->setParameter(':group_id', $groupId)
            ->execute();
    }
}
