<?php

namespace app\backend\users;

use infrastructure\Database;

readonly class UserFinder
{
    public function __construct(
        private Database $database
    ) { }

    function find(): array
    {
        $query =
            "select users.*, session.last_activity_at
            from users
            left outer
                join (select user_id, MAX(last_activity_at) as last_activity_at
                    from sessions
                    where last_activity_at > (now() - interval '15 minutes')
                    group by user_id ) AS session
                on session.user_id = users.id";
        return $this->database->fetch($query);
    }
}
