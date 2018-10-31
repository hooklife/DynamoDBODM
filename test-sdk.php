<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/10/18
 * Time: 1:34
 */

use Aws\DynamoDb\Exception\DynamoDbException;
require "vendor/autoload.php";

$dynamodb = new \Aws\DynamoDb\DynamoDbClient([
    'region'   => 'ap-southeast-1',
    'version'  => '2012-08-10',
    'credentials' => [
        'key' => "###",
        'secret'  => "###",
    ],
]);


$marshaler = new \Aws\DynamoDb\Marshaler();

$tableName = 'test';

$year = 2015;
$title = 'The Big New Movie';

$item = $marshaler->marshalJson('
    {
        "year": ' . $year . ',
        "title": "' . $title . '",
        "info": {
            "plot": "Nothing happens at all.",
            "rating": 0
        },
        "aa": {
            "aa":"aaa"
        }
    }
');

$params = [
    'TableName' => $tableName,
    'Item' => $item
];


try {
    $result = $dynamodb->putItem($params);
    echo "Added item: $year - $title\n";

} catch (DynamoDbException $e) {
    echo "Unable to add item:\n";
    echo $e->getMessage() . "\n";
}