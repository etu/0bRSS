<?php
namespace ZerobRSS\Dao;

use \Doctrine\DBAL\Connection as Db;

class Groups
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getGroup($value, $column = 'id')
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('groups')
            ->where($column.' = :value')
            ->setParameter(':value', $value)
            ->execute();
    }
}
