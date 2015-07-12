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

    public function getFeedsToUpdate()
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('feeds')
            ->where('next_update < NOW()')
            ->execute();
    }

    public function update($id, $values)
    {
        // Prepare update query
        $query = $this->db->createQueryBuilder()
               ->update('feeds')
               ->where('id = :id')
               ->setParameter(':id', $id);

        // Append parameters to update to the query
        foreach ($values as $key => $value) {
            $query = $query->set($key, ':'.$key)->setParameter(':'.$key, $value);
        }

        return $query->execute();
    }
}
