<?php
namespace ZerobRSS\Dao;

use \Doctrine\DBAL\Connection as Db;

class Users
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getUser(string $value, string $column = 'id') : \stdClass
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where($column.' = :value')
            ->setParameter(':value', $value)
            ->execute();
    }

    public function update(int $id, array $values) : bool
    {
        // Prepare update query
        $query = $this->db->createQueryBuilder()
               ->update('users')
               ->where('id = :id')
               ->setParameter(':id', $id);

        // Append parameters to update to the query
        foreach ($values as $key => $value) {
            $query = $query->set($key, ':'.$key)->setParameter(':'.$key, $value);
        }

        return $query->execute();
    }

    public function create(array $values) : int
    {
        // Prepare insert query
        $query = $this->db->createQueryBuilder()
               ->insert('users');

        // Append parameters to insert to the query
        foreach ($values as $key => $value) {
            $query = $query->setValue($key, ':'.$key)->setParameter(':'.$key, $value);
        }

        $query->execute();

        // @TODO: Check if this works in MariaDB
        return $this->db->lastInsertId('users_id_seq');
    }
}
