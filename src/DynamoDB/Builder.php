<?php

namespace Hoooklife\DynamodbPodm\DynamoDB;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Closure;
use Hoooklife\DynamodbPodm\ComparisonOperator;
use Hoooklife\DynamodbPodm\Concerns\HasParsers;
use Hoooklife\DynamodbPodm\DB;
use Hoooklife\DynamodbPodm\Grammars\DynamoDBBuilder;
use Hoooklife\DynamodbPodm\Grammars\DynamoDBGrammar;

class Builder
{
    use HasParsers;
    /**
     * 查询排序
     */
    public $limit;
    public $wheres = [];
    public $bindings = [];
    public $columns;
    public $table;


    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!='
    ];

    public $reservedKey = [
        'key'
    ];

    /** @var DynamoDBGrammar */
    private $dynamoDBBuilder;
    private $client;

    public function __construct($connection = 'default')
    {
        $this->setupExpressions();
        switch (DB::$config[$connection]['driver']) {
            case "dynamedb":
                $this->dynamoDBBuilder = new DynamoDBBuilder(DB::$config[$connection]);
                $this->client = new DynamoDbClient(DB::$config[$connection]["S3Config"]);
                break;
            default:
                throw new \Exception("bad driver");
        }
    }


    /**
     * Set the table which the query is targeting.
     *
     * @param  string $table
     * @return $this
     */
    public function from($table)
    {
        $this->table = $table;
        return $this;
    }


    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure $column
     * @param  mixed $operator
     * @param  mixed $value
     * @param  string $boolean
     * @param string $type
     * @return $this
     */

    public function where($column, $operator = null, $value = null, $type = "key", $boolean = 'and')
    {
        if (is_array($column)) {
            // 递归
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $this->where(...array_values($value));
                } else {
                    $this->where($key, '=', $value, $type, $boolean);
                }
            }
            return $this;
        }

        if (func_num_args() === 2 || !ComparisonOperator::isValidOperator($operator)) {
            list($value, $operator) = [$operator, '='];
        }

//        if ($column instanceof Closure) {
//            return $this->whereNested($column, $boolean);
//        }


        // where in
        if (is_array($value)) {
            return $this->whereIn($column, $type, $boolean);
        }

        // is null
        if (is_null($value)) {
            return $this->whereNull($column, $type, $boolean, $operator !== '=');
        }

        $this->wheres[] = [
            'column'   => $column,
            'operator' => ComparisonOperator::getDynamoDbOperator($operator),
            'value'    => $value,
            'boolean'  => $boolean,
            'type'     => $type
        ];

        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string $column
     * @param  array $values
     * @param string $type
     * @param  string $boolean
     * @return $this
     */
    public function whereIn($column, $values, $type = "key", $boolean = 'and')
    {
        return $this->where($column, ComparisonOperator::IN, $values, $type, $boolean);
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param  string $column
     * @param  string $boolean
     * @param  bool $not
     * @return $this
     */
    public function whereNull($column, $type = "key", $boolean = 'and', $not = false)
    {
        $operator = $not ? ComparisonOperator::NOT_NULL : ComparisonOperator::NULL;
        $this->wheres[] = compact('column', 'operator', 'boolean', 'type');
        return $this;
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param  string $column
     * @param string $type
     * @return $this
     */
    public function orWhereNull($column, $type = "key")
    {
        return $this->whereNull($column, $type, 'or');
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param  string $column
     * @param string $type
     * @return $this
     */
    public function orWhereNotNull($column, $type = "key")
    {
        return $this->whereNotNull($column, $type, 'or');
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param  string $column
     * @param string $type
     * @param  string $boolean
     * @return $this
     */
    public function whereNotNull($column, $type = "key", $boolean = 'and')
    {
        return $this->whereNull($column, $type, $boolean, true);
    }


    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    public function limit($value)
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toQuery()
    {
        return $this->grammar->compileSelect($this);
    }


    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string $column
     * @return mixed
     */
    public function value($column)
    {
        $result = $this->first([$column])->toArray();
        return $result !== null ? reset($result) : null;
    }


    /**
     * Execute the query and get the first result.
     *
     * @param  array $columns
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function first($columns = ['*'])
    {
        return $this->take(1)->all($columns)->first();
    }

    public function get($columns = [])
    {
        $raw = $this->toDynamoDbQuery($columns, 10);
        if ($raw->op === 'Scan') {
            $res = $raw->scan($raw->query);
        } else {
            $res = $this->client->query($raw->query);
            $res = $res['Items'];
        }

        foreach ($res as $item) {
            $results[] = (new Marshaler())->unmarshalItem($item);
        }
        return $results;
    }

    public function query($columns = [])
    {
        $limit = isset($this->limit) ?: -1;

    }

    public function toDynamoDBQuery($columns, $limit)
    {
        if (!empty($this->wheres)) {
            $this->dynamoDBBuilder->setTableName($this->table);
            $this->dynamoDBBuilder->setKeyConditionExpression($this->keyConditionExpression->parse($this->wheres));
            foreach ($this->wheres as $where) {
//                if ($where['type'] === 'key') {
//                } else {
//                    $this->dynamoDBBuilder->setFilterExpression($this->filterExpression->parse($where));
//                }
            }
        }

        $this->dynamoDBBuilder->setLimit($limit);
        if (!empty($columns)) {
            $this->dynamoDBBuilder->setProjectionExpression($this->projectionExpression->parse($columns));
        }

        $this->dynamoDBBuilder
            ->setExpressionAttributeNames($this->expressionAttributeNames->all())
            ->setExpressionAttributeValues($this->expressionAttributeValues->all());

        $op = 'Query';
        $raw = new RawDynamoDbQuery($op, $this->dynamoDBBuilder->prepare()->query);

        return $raw;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array $columns
     * @return Collection
     */
    public function all($columns = ['*']): Collection
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->columns = $columns;

        return $this->grammar->all();
    }


    public function getGrammar()
    {
        return $this->grammar;
    }

    public function getBuilder()
    {
        return $this->grammar->getBuilder();
    }
}
