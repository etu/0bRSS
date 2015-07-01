<?php
namespace ZerobRSS\Dao;

use \Doctrine\DBAL\Query\QueryBuilder as QB;

class Users
{
    /** @var QB */
    private $qb;

    public function __construct(QB $qb)
    {
        $this->qb = $qb;
    }

    public function getUser($value, $column = 'id')
    {
        return $this->qb
            ->select('*')
            ->from('users')
            ->where($column.' = :value')
            ->setParameter(':value', $value)
            ->execute();
    }
}
