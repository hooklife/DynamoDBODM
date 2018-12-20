<?php

namespace Hoooklife\DynamodbPodm\Grammars;

use Aws\DynamoDb\DynamoDbClient;

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
 * @method DynamoDBBuilder setFilterExpression(string $expression)
 * @method DynamoDBBuilder setUpdateExpression(string $expression)
 * @method DynamoDBBuilder setAttributeUpdates(array $updates)
 * @method DynamoDBBuilder setConsistentRead(bool $consistent)
 * @method DynamoDBBuilder setScanIndexForward(bool $forward)
 * @method DynamoDBBuilder setExclusiveStartKey(mixed $key)
 * @method DynamoDBBuilder setReturnValues(string $type)
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
    public $batchWriteItem = [];

    /** @var $dynamodbClient DynamoDbClient */
    protected $dynamodbClient;

    public function __construct(array $config)
    {
        $this->dynamodbClient = new DynamoDbClient($config["S3Config"]);
    }

    public function hydrate(array $query)
    {
        $this->query = $query;
        return $this;
    }

    public function setExpressionAttributeName($placeholder, $name)
    {
        $this->query['ExpressionAttributeNames'][$placeholder] = $name;
        return $this;
    }

    public function setExpressionAttributeValue($placeholder, $value)
    {
        $this->query['ExpressionAttributeValues'][$placeholder] = $value;
        return $this;
    }

    public function setKeyConditionExpression($mapping)
    {
        if ($mapping) {
            $this->query['KeyConditionExpression'] = $mapping;
        }
        return $this;
    }

    public function setProjectionExpression($expression)
    {
        if ($expression) {
            $this->query['ProjectionExpression'] = $expression;
        }
        return $this;
    }

    public function setExpressionAttributeValues($mapping)
    {
        if ($mapping) {
            $this->query['ExpressionAttributeValues'] = $mapping;
        }
        return $this;
    }

    public function setRequestItems($items)
    {
        $this->batchWriteItem['RequestItems'] = $items;
        return $this;
    }

    public function batchWriteItem()
    {
//        var_dump($this->batchWriteItem);
        return $this->dynamodbClient->batchWriteItem($this->batchWriteItem);
    }

    public function scan()
    {
        return $this->dynamodbClient->scan($this->query);
    }

    public function query()
    {
//        var_dump($this->query);
        return $this->dynamodbClient->query($this->query);
    }

    /**
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'set') === 0) {
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