<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2018/12/5
 * Time: 下午4:59
 */

namespace Hoooklife\DynamodbPodm;


use Aws\DynamoDb\Marshaler;
use Aws\Result;

class Collection implements \ArrayAccess
{
    /** @var Result $data */
    private $data;
    private $marshaler;
    /**
     * @var bool
     */
    private $many;

    public function __construct($data, $many = true)
    {
        $this->data = $data;

        $this->many = $many;
    }

    public function __get($var)
    {
        var_dump(1);
        die;
    }

    private function getMarshaler()
    {
        if (!$this->marshaler) {
            $this->marshaler = new Marshaler();
        }
        return $this->marshaler;

    }


    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }


    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->getMarshaler()->unmarshalItem($this->data[$offset]);
        }
        return null;
    }


    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }


    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function toArray()
    {
        if (!isset($this->data["Items"]) || !$this->data["Items"]) {
            return null;
        }
        if (!$this->many) {
            return $this->getMarshaler()->unmarshalItem(
                reset($this->data["Items"])
            );
        }

        $result = [];
        foreach ($this->data["Items"] as $item) {
            $result[] = $this->getMarshaler()->unmarshalItem($item);
        }
        return $result;
    }

    public function first(): Collection
    {
        return new Collection($this->data, false);
    }
}