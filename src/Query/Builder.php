<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/10/18
 * Time: 2:35
 */

class Builder
{
    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public $columns;


    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
}