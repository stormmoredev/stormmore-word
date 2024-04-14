<?php

namespace backend\sessions;

use infrastructure\Database;

class SessionFinder
{
    public function __construct(
        private Database $database
    ) { }

    public function find(Criteria $criteria): array
    {
        $query =
            "SELECT s.id, u.name, s.valid_to, s.last_activity_at, s.created_at    
            FROM sessions as s
            LEFT JOIN users u on s.user_id = u.id
            order by s.created_at desc
            OFFSET ? LIMIT ?";
        return $this->database->fetch($query, $criteria->getOffset() , $criteria->getLimit());
    }

    public function count(Criteria $criteria)
    {
        $query =
            'SELECT count(*) as count    
            FROM sessions as s
            LEFT JOIN users u on s.user_id = u.id';

        return $this->database->fetchOne($query)->count;
    }
}