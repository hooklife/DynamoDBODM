<?php

namespace Hoooklife\DynamodbPodm\Grammars;

use Aws\DynamoDb\Marshaler;
use Hoooklife\DynamodbPodm\Collection;
use Hoooklife\DynamodbPodm\DB;
use Hoooklife\DynamodbPodm\Query\Builder;
use Hoooklife\DynamodbPodm\Grammars\DynamoDBBuilder;

class DynamoDBGrammar
{
    protected $operators = [];

    protected $params = [];

    protected $insertParam = [];

    /**
     * @var Builder
     */
    private $builder;
    /**
     * @var array
     */
    private $config;

    private $dynamoDBBuilder;

    private $attributeValues = [];

    /** @var Marshaler $marshaler */
    private $marshaler;

    /**
     * DynamoDBGrammar constructor.
     * @param Builder $builder
     * @param array $config
     */
    public function __construct(Builder $builder, array $config)
    {
        $this->builder = $builder;
        $this->config = $config;

        $this->dynamoDBBuilder = new DynamoDBBuilder($config);
        $this->marshaler = new Marshaler();

    }

    // 表达式解析 where
    private function parseKeyConditionExpression()
    {
        $expression = [];
        foreach ($this->builder->wheres as $index => $where) {
            $expression[] = "{$where['column']} {$where['operator']} :{$index}";
            // param bind
            $this->attributeValues[':' . $index] = $where['value'];
        }
        return implode("and", $expression);
    }

    public function parseExpressionAttributeValues()
    {
        return $this->marshaler->marshalItem($this->attributeValues);
    }

    // select
    public function parseProjectionExpression()
    {
        if (reset($this->builder->columns) != '*') {
            return implode(",", $this->columns);
        }
        return null;
    }

    // limit
    public function parseLimit()
    {
        return $this->builder->limit;
    }

    /**
     * Get the grammar specific operators.
     *
     * @return array
     */
    public function getOperators()
    {
        return $this->operators;
    }

    public function insert($data)
    {
        $this->createInsertParam($data);

        $builder = $this->dynamoDBBuilder
            ->setRequestItems([$this->builder->table => $this->insertParam]);

        return $builder->batchWriteItem();
    }

    protected function createInsertParam($data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key) && is_array($value)) {
                $this->createInsertParam($value);
            } else {
                $this->insertParam[] = [
                    'PutRequest' => [
                        'Item' => $this->marshaler->marshalItem($data)
                    ]
                ];
                break;
            }
        }
    }

    public function all($columns): Collection
    {
        $builder = $this->dynamoDBBuilder
            ->setTableName($this->builder->table)
            ->setKeyConditionExpression($this->parseKeyConditionExpression())
            ->setExpressionAttributeValues($this->parseExpressionAttributeValues());
        return new Collection($builder->query());
    }


}