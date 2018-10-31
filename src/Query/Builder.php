<?php
 
namespace Hoooklife\DynamodbPodm\Query;
use Hoooklife\DynamodbPodm\DB;
use Hoooklife\DynamodbPodm\Grammars\DynamoDBGrammar;
class Builder{

    /**
     * 查询排序
     */
    public $limit;
    public $where;


    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!='
    ];

    public function __construct($connection = 'default')
    {
        switch (DB::$config[$connection]['driver']) {
            case "dynamedb":
               $this->grammar = new DynamoDBGrammar();
               break;
            default:
                throw new Exception("bad driver");
        }        
    }


    /**
     * 设置要查出的列
     *
     * @param  array|mixed  $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }


    /**
     * Add a new select column to the query.
     *
     * @param  array|mixed  $column
     * @return $this
     */
    public function addSelect($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->columns = array_merge((array) $this->columns, $columns);
        return $this;
    }


    /**
     * Set the table which the query is targeting.
     *
     * @param  string  $table
     * @return $this
     */
    public function from($table)
    {
        $this->from = $table;
        return $this;
    }
    


    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure  $column
     * @param  mixed   $operator
     * @param  mixed   $value
     * @param  string  $boolean
     * @return $this
     */

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            // 递归
            foreach ($column as $key => $value) {
                 if (is_numeric($key) && is_array($value)) {
                    $this->where(...array_values($value));
                } else {
                    $this->where($key, '=', $value, $boolean);
                }
            }
            return $this;
            // return $this->addArrayOfWheres($column, $boolean);
        }

        if(func_num_args() === 2 || $this->invalidOperator($operator)) {
            list($value, $operator) = [$operator, '='];
        }

        // where in
        if (is_array($value)){
            return $this->whereIn($column,$boolean);
        }

        // is null
        if (is_null($value)) {
            return $this->whereNull($column, $boolean, $operator !== '=');
        }


        $type = 'Basic';
        $this->wheres[] = compact(
            'type', 'column', 'operator', 'value', 'boolean'
        );


        // TODO 参数绑定
        // $this->addBinding($value, 'where');

        return $this;
    }


     /**
     * Determine if the given operator is supported.
     *
     * @param  string  $operator
     * @return bool
     */
    protected function invalidOperator($operator)
    {
        return ! in_array(strtolower($operator), $this->operators, true) &&
               ! in_array(strtolower($operator), $this->grammar->getOperators(), true);
    }


    // /**
    //  * 向查询添加排序语句。
    //  *
    //  * @param  string  $column
    //  * @param  string  $direction
    //  * @return $this
    //  */
    // public function orderBy($columns, $direction = 'asc')
    // {
    //     $orders = is_array($columns) ? $columns : [$columns => $direction];
    //     $this->orders = array_merge($this->orders, $orders);
    //     return $this;
    // }

    // public function offset($value)
    // {
    //     $this->offset =  max(0, $value);
    //     return $this;
    // }

    //  /**
    //  * Alias to set the "offset" value of the query.
    //  *
    //  * @param  int  $value
    //  * @return \Illuminate\Database\Query\Builder|static
    //  */
    // public function skip($value)
    // {
    //     return $this->offset($value);
    // }


    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int  $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function take($value)
    {
        return $this->limit($value);
    }


    /**
     * Set the "limit" value of the query.
     *
     * @param  int  $value
     * @return $this
     */
    public function limit($value)
    {
        //  FIXME ??? 为啥等于0
        if ($value > 0) {
            $this->limit = $value;
        }
        return $this;
    }

     /**
     * Set the limit and offset for a given page.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function forPage($page, $perPage = 15)
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }


     /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toSql()
    {
        return $this->grammar->compileSelect($this);
    }


    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string  $column
     * @return mixed
     */
    public function value($column)
    {
        $result = (array) $this->first([$column]);
        return count($result) > 0 ? reset($result) : null;
    }



    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function first($columns = ['*'])
    {
        return reset($this->take(1)->all($columns));
    }


    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Support\Collection
     */
    public function all($columns = ['*'])
    {
        return $this->grammar->all();
    }

}
