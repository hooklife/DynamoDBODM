<?php
namespace Hoooklife\DynamodbPodm\Grammars;
use Aws\DynamoDb\Marshaler;
use Hoooklife\DynamodbPodm\DB;
use Hoooklife\DynamodbPodm\Query\Builder;

class DynamoDBGrammar {
    protected $operators = [];

    protected $params = [];

    /**
     * @var Builder
     */
    private $builder;
    /**
     * @var array
     */
    private $config;

    /**
     * DynamoDBGrammar constructor.
     * @param Builder $builder
     * @param array $config
     */
    public function __construct(Builder $builder, array $config)
    {
        $this->builder = $builder;
        $this->config = $config;
    }

    // 表达式解析 where
    public function parseKeyConditionExpression(){
        $expression = [];
        foreach ($this->builder->wheres as $where) {
            $expression[] = "{$where['column']} {$where['operator']} {$where['value']}";
        }

        return implode("and", $expression);

    }

    // select
    public function parseProjectionExpression()
    {
        if( reset($this->builder->columns) != '*'  ){
            return implode(",", $this->columns);
        }
        return null;
    }

    // limit
    public function parseLimit(){
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

    public function all()
    {
        $dynamodb = new \Aws\DynamoDb\DynamoDbClient($this->config["S3Config"]);

//        var_dump((new Marshaler())->marshalItem([
//            ':title'=>'aaa'
//        ]));
//        var_dump((new Marshaler())->marshalJson('
//            {
//                ":title": "aaa"
//            }
//        '));
//        die;
        $params = [
            'TableName' => $this->builder->table,
            'KeyConditionExpression' => 'title = :title',
            'FilterExpression' => 'title = :title',
            'ExpressionAttributeValues'=> (new Marshaler())->marshalItem([
                ':title'=>'The Big New Movie'
            ])
        ];

        $result = $dynamodb->scan($params);

        var_dump($result);
    }
}