<?php

use BadMethodCallException;

/**
 * Class QueryBuilder
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
 * @method QueryBuilder setExpressionAttributeNames(array $mapping)
 * @method QueryBuilder setExpressionAttributeValues(array $mapping)
 * @method QueryBuilder setFilterExpression(string $expression)
 * @method QueryBuilder setKeyConditionExpression(string $expression)
 * @method QueryBuilder setProjectionExpression(string $expression)
 * @method QueryBuilder setUpdateExpression(string $expression)
 * @method QueryBuilder setAttributeUpdates(array $updates)
 * @method QueryBuilder setConsistentRead(bool $consistent)
 * @method QueryBuilder setScanIndexForward(bool $forward)
 * @method QueryBuilder setExclusiveStartKey(mixed $key)
 * @method QueryBuilder setReturnValues(string $type)
 * @method QueryBuilder setRequestItems(array $items)
 * @method QueryBuilder setTableName(string $table)
 * @method QueryBuilder setIndexName(string $index)
 * @method QueryBuilder setSelect(string $select)
 * @method QueryBuilder setItem(array $item)
 * @method QueryBuilder setKeys(array $keys)
 * @method QueryBuilder setLimit(int $limit)
 * @method QueryBuilder setKey(array $key)
 */
class DynamoDBBuilder{
    /**
     * Query body to be sent to AWS
     *
     * @var array
     */
    public $query = [];

    /**
     * @param  string $method
     * @param  array  $parameters
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
}