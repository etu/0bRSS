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


    /**
     * Get one or all feeds for a user
     */
    public function getFeeds($userId, $feedId = null)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        // Prepare default where-clause
        $whereClause = 'user_id = :user_id';

        // But if we got a feedId, make a where expression and add the feed_id parameter
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


    // Used by cronjob to get feeds that needs to be updated
    public function getFeedsToUpdate()
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('feeds')
            ->where('next_update < NOW()')
            ->execute();
    }


    // Update settings for feed
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


    // Add feed
    public function create($userId, $values)
    {
        // Prepare insert query
        $query = $this->db->createQueryBuilder()
            ->insert('feeds')
            ->setValue('user_id', ':user_id')
            ->setParameter(':user_id', $userId);

        // Append parameters to insert to the query
        foreach ($values as $key => $value) {
            $query = $query->setValue($key, ':'.$key)->setParameter(':'.$key, $value);
        }

        $query->execute();

        // @TODO: Check if this works in MariaDB
        return $this->db->lastInsertId('feeds_id_seq');
    }
}
