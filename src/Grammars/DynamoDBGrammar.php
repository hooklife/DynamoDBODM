<?php

namespace Hoooklife\DynamodbPodm\Grammars;

use Aws\DynamoDb\Marshaler;
use Hoooklife\DynamodbPodm\Collection;
use Hoooklife\DynamodbPodm\DB;
use Hoooklife\DynamodbPodm\Query\Builder;
use Hoooklife\DynamodbPodm\Grammars\DynamoDBBuilder;

class DynamoDBGrammar
{
    protected $operators = [
        'begins_with'
    ];

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

    /** @var \Hoooklife\DynamodbPodm\Grammars\DynamoDBBuilder $dynamoDBBuilder */
    private $dynamoDBBuilder;

    private $attributeValues = [];
    private $attributeNames = [];


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

        foreach ($this->builder->wheres as $valueIndex => $where) {
            $columnIndex = count($this->attributeNames);
            switch (strtolower($where['operator'])) {
                case "begins_with":
                    $expression[] = "begins_with(#$columnIndex, :{$valueIndex})";
                    break;
                default:
                    $expression[] = "#$columnIndex {$where['operator']} :{$valueIndex}";
            }

            // param bind
            $this->attributeValues[':' . $valueIndex] = $where['value'];
            $this->attributeNames['#' . $columnIndex] = $where['column'];
        }
        return implode(" and ", $expression);
    }

    public function parseExpressionAttributeValues()
    {
        return $this->marshaler->marshalItem($this->attributeValues);
    }


    // select
    public function parseProjectionExpression()
    {
        $tmp = [];
        foreach ($this->builder->columns as $column) {
            $index = count($this->attributeNames);
            $this->attributeNames["#{$index}"] = $column;
            $tmp[] = "#{$index}";
        }
        return implode(",", $tmp);

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


    public function all(): Collection
    {

        $builder = $this->dynamoDBBuilder
            ->setTableName($this->builder->table)
            ->setKeyConditionExpression($this->parseKeyConditionExpression())
            ->setExpressionAttributeValues($this->parseExpressionAttributeValues())
            ->setProjectionExpression($this->parseProjectionExpression())
            ->setExpressionAttributeNames($this->attributeNames)
            ->prepare()
            ->query();
    }

    public function delete()
    {
        $builder = $this->dynamoDBBuilder
            ->setTableName($this->builder->table)
            ->setExpressionAttributeValues($this->parseExpressionAttributeValues());

    }

    public function getBuilder()
    {
        return $this->dynamoDBBuilder;
    }
}