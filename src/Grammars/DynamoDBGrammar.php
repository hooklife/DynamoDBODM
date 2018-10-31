<?php
namespace Hoooklife\DynamodbPodm\Grammars;
class DynamoDBGrammar{
    protected $operators = [];

    protected $params = [];

    // 表达式解析 where
    public function parseKeyConditionExpression(){
        $expression = [];
        foreach ($this->wheres as $where) {
            $expression[] = "{$where['column']} {$where['operator']} {$where['value']}";
        }

        return implode("and", $expression);

    }

    // select
    public function parseProjectionExpression()
    {
        if( reset($this->columns) != '*'  ){
            return implode(",", $this->columns);
        }
        return null;
    }

    // limit
    public function parseLimit(){
        return $this->limit;
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
        
    }
}