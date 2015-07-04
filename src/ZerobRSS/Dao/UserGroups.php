<?php
namespace ZerobRSS\Dao;

use \Doctrine\DBAL\Connection as Db;

class UserGroups
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getUserGroups($value, $column = 'user_id')
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('user_groups', 'ug')
            ->innerJoin('ug', 'groups', 'g', 'g.id = ug.group_id')
            ->where($column.' = :value')
            ->setParameter(':value', $value)
            ->execute();
    }
}
