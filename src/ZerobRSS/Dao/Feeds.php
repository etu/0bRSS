<?php
namespace ZerobRSS\Dao;

use \Doctrine\DBAL\Connection as Db;

class Feeds
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getFeeds($userId)
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('feeds')
            ->where('user_id = :user_id')
            ->setParameter(':user_id', $userId)
            ->execute();
    }
}
