<?php
declare(strict_types=1);

namespace ZerobRSS\Dao;

use Doctrine\DBAL\Connection as Db;

class UserApiTokens
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function createTokenForUser(int $userId) : string
    {
        // Set up valid characters
        $chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9), [
            '=', '!', '@', '#', '/', '(', ')', '{', '[', ']', '}', '_',
        ]);

        while (true) {
            $token = '';

            for ($i = 0; $i < 64; $i++) {
                $token .= $chars[rand(0, count($chars) - 1)];
            }

            try {
                $this->db->createQueryBuilder()
                    ->insert('user_api_tokens')
                    ->setValue('user_id', ':user_id')
                    ->setValue('token', ':token')
                    ->setValue('expires', ':expires')
                    ->setParameter(':user_id', $userId)
                    ->setParameter(':token', $token)
                    ->setParameter(':expires', date('Y-m-d H:i:s', strtotime('+30 days')))
                    ->execute();

                return $token;
            } catch (\Exception $e) {
                // pass
            }
        }
    }

    public function validateUserToken(string $token) : bool
    {
        $token = $this->db->createQueryBuilder()
            ->select('uat.*')
            ->from('user_api_tokens', 'uat')
            ->where('uat.token = :token')
            ->setParameter(':token', $token)
            ->execute()
            ->fetch();

        return strtotime($token->expires) > time();
    }
}
