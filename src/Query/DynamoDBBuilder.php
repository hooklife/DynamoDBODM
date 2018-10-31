<?php

namespace Hoooklife\DynamodbPodm\Query;
/**
 * Class DynamoDBBuilder
 *
 * @package BaoPham\DynamoDb\DynamoDb
 *
 * Methods are in the form of `set<key_name>`, where `<key_name>`
 * is the key name of the query body to be sent.
 *
 * For example, to build a query:
 * [
 *     'AttributeDefinitions' => ...,
 *     'GlobalSecondaryIndexUpdates' => ...
 *     'TableName' => ...
 * ]
 *
 * Do:
 *
 * $query = $query->setAttributeDefinitions(...)->setGlobalSecondaryIndexUpdates(...)->setTableName(...);
 *
 * When ready:
 *
 * $query->prepare()->updateTable();
 *
 * Common methods:
 *
 * @method DynamoDBBuilder setExpressionAttributeNames(array $mapping)
 * @method DynamoDBBuilder setExpressionAttributeValues(array $mapping)
 * @method DynamoDBBuilder setFilterExpression(string $expression)
 * @method DynamoDBBuilder setKeyConditionExpression(string $expression)
 * @method DynamoDBBuilder setProjectionExpression(string $expression)
 * @method DynamoDBBuilder setUpdateExpression(string $expression)
 * @method DynamoDBBuilder setAttributeUpdates(array $updates)
 * @method DynamoDBBuilder setConsistentRead(bool $consistent)
 * @method DynamoDBBuilder setScanIndexForward(bool $forward)
 * @method DynamoDBBuilder setExclusiveStartKey(mixed $key)
 * @method DynamoDBBuilder setReturnValues(string $type)
 * @method DynamoDBBuilder setRequestItems(array $items)
 * @method DynamoDBBuilder setTableName(string $table)
 * @method DynamoDBBuilder setIndexName(string $index)
 * @method DynamoDBBuilder setSelect(string $select)
 * @method DynamoDBBuilder setItem(array $item)
 * @method DynamoDBBuilder setKeys(array $keys)
 * @method DynamoDBBuilder setLimit(int $limit)
 * @method DynamoDBBuilder setKey(array $key)
 */
class DynamoDBBuilder
{
    /**
     * Query body to be sent to AWS
     *
     * @var array
     */
    public $query = [];

    /**
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'set')) {
            $key = array_reverse(explode('set', $method, 2))[0];
            $this->query[$key] = current($parameters);
            return $this;
        }
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }

    public function exec()
    {

    }
}