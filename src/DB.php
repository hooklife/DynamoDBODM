<?php
namespace Hoooklife\DynamodbPodm;
use Hoooklife\DynamodbPodm\Query\Builder;
class DB {
    public static $config = [];

    public static function config(array $config)
    {
        self::$config = $config;
    }

    public static function table(string $table) {
        $query = new Builder();
        $query->from($table);
        return $query;
    }
    

}