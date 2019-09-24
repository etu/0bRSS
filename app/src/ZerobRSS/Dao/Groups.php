<?php
declare(strict_types=1);

namespace ZerobRSS\Dao;

use Doctrine\DBAL\Connection as Db;
use PDOStatement;

class Groups
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getGroup(string $value, string $column = 'id') : PDOStatement
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('groups')
            ->where($column.' = :value')
            ->setParameter(':value', $value)
            ->execute();
    }
}
