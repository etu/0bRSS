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

    public function getFeeds($userId, $feedId = null)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $whereClause = 'user_id = :user_id';

        if (null !== $feedId) {
            $whereClause = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('user_id', ':user_id'),
                $queryBuilder->expr()->eq('id', ':feed_id')
            );

            $queryBuilder->setParameter(':feed_id', $feedId);
        }

        $query = $queryBuilder
            ->select('id, name, website_uri, feed_uri, description, added, updated, next_update, update_interval')
            ->addSelect('(SELECT COUNT(*) FROM articles WHERE read = false and feed_id = feeds.id) as unread')
            ->from('feeds')
            ->where($whereClause)
            ->setParameter(':user_id', $userId);


        return $query->execute();
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
