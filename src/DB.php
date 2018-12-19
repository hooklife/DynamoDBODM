<?php

namespace Hoooklife\DynamodbPodm;

use Hoooklife\DynamodbPodm\Query\Builder;

class DB
{
    public static $config = [];
    public static $connect;

    public static function config(array $config, string $connect = 'default')
    {
        self::$config = $config;
        self::$connect = $connect;

    }

    public static function table(string $table)
    {
        $query = new Builder(self::$connect);
        $query->from($table);
        return $query;
    }


}