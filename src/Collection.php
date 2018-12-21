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

    public function __construct($data)
    {
        $this->data = $data;

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

        $result = [];
        foreach ($this->data["Items"] as $item) {
            $result[] = $this->getMarshaler()->unmarshalItem($item);
        }
        return $result;
    }

    public function first()
    {
        $item = reset($this->data["Items"]);
        return $this->getMarshaler()->unmarshalItem($item);
    }
}