<?php

namespace Hoooklife\DynamodbPodm\Parsers;

use Aws\DynamoDb\Marshaler;
use Hoooklife\DynamodbPodm\ComparisonOperator;
use Hoooklife\DynamodbPodm\NotSupportedException;
use Hoooklife\DynamodbPodm\Facades\DynamoDb;

class ConditionExpression
{
    const OPERATORS = [
        ComparisonOperator::EQ           => '%s = :%s',
        ComparisonOperator::LE           => '%s <= :%s',
        ComparisonOperator::LT           => '%s < :%s',
        ComparisonOperator::GE           => '%s >= :%s',
        ComparisonOperator::GT           => '%s > :%s',
        ComparisonOperator::BEGINS_WITH  => 'begins_with(%s, :%s)',
        ComparisonOperator::BETWEEN      => '(%s BETWEEN :%s AND :%s)',
        ComparisonOperator::CONTAINS     => 'contains(%s, :%s)',
        ComparisonOperator::NOT_CONTAINS => 'NOT contains(%s, :%s)',
        ComparisonOperator::NULL         => 'attribute_not_exists(%s)',
        ComparisonOperator::NOT_NULL     => 'attribute_exists(%s)',
        ComparisonOperator::NE           => '%s <> :%s',
        ComparisonOperator::IN           => '%s IN (%s)',
    ];

    /**
     * @var ExpressionAttributeValues
     */
    protected $values;

    /**
     * @var ExpressionAttributeNames
     */
    protected $names;

    /**
     * @var Placeholder
     */
    protected $placeholder;

    public function __construct(
        Placeholder $placeholder,
        ExpressionAttributeValues $values,
        ExpressionAttributeNames $names
    )
    {
        $this->placeholder = $placeholder;
        $this->values = $values;
        $this->names = $names;
    }

    /**
     * @param array $where
     *   [
     *     'column' => 'name',
     *     'type' => 'EQ',
     *     'value' => 'foo',
     *     'boolean' => 'and',
     *     'type' => 'key'
     *   ]
     *
     * @return string
     */
    public function parse($wheres)
    {
        if (empty($wheres)) {
            return '';
        }

        $parsed = [];

        foreach ($wheres as $condition) {
            $boolean = array_get($condition, 'boolean');
            $value = array_get($condition, 'value');
            $operator = array_get($condition, 'operator');

            $prefix = '';

            if (count($parsed) > 0) {
                $prefix = strtoupper($boolean) . ' ';
            }

            if ($operator === 'Nested') {
                $parsed[] = $prefix . $this->parseNestedCondition($value);
                continue;
            }
            $parsed[] = $prefix . $this->parseCondition(
                    array_get($condition, 'column'),
                    $operator,
                    $value
                );
        }

        return implode(' ', $parsed);
    }

    public function reset()
    {
        $this->placeholder->reset();
        $this->names->reset();
        $this->values->reset();
    }

    protected function getSupportedOperators()
    {
        return static::OPERATORS;
    }

    protected function parseNestedCondition(array $conditions)
    {
        return '(' . $this->parse($conditions) . ')';
    }

    protected function parseCondition($name, $operator, $value)
    {
        $operators = $this->getSupportedOperators();
        if (empty($operators[$operator])) {
            throw new NotSupportedException("$operator is not supported");
        }

        $template = $operators[$operator];

        $this->names->set($name);

        if ($operator === ComparisonOperator::BETWEEN) {
            return $this->parseBetweenCondition($name, $value, $template);
        }

        if ($operator === ComparisonOperator::IN) {
            return $this->parseInCondition($name, $value, $template);
        }

        if ($operator === ComparisonOperator::NULL || $operator === ComparisonOperator::NOT_NULL) {
            return $this->parseNullCondition($name, $template);
        }

        $placeholder = $this->placeholder->next();

        $this->values->set($placeholder, (new Marshaler())->marshalValue($value));

        return sprintf($template, $this->names->placeholder($name), $placeholder);
    }

    protected function parseBetweenCondition($name, $value, $template)
    {
        $first = $this->placeholder->next();

        $second = $this->placeholder->next();

        $this->values->set($first, DynamoDb::marshalValue($value[0]));

        $this->values->set($second, DynamoDb::marshalValue($value[1]));

        return sprintf($template, $this->names->placeholder($name), $first, $second);
    }

    protected function parseInCondition($name, $value, $template)
    {
        $valuePlaceholders = [];

        foreach ($value as $item) {
            $placeholder = $this->placeholder->next();

            $valuePlaceholders[] = ":" . $placeholder;

            $this->values->set($placeholder, DynamoDb::marshalValue($item));
        }

        return sprintf($template, $this->names->placeholder($name), implode(', ', $valuePlaceholders));
    }

    protected function parseNullCondition($name, $template)
    {
        return sprintf($template, $this->names->placeholder($name));
    }
}

function array_get($array, $key, $default = null)
{
    if (is_null($key)) {
        return $array;
    }

    if (isset($array[$key])) {
        return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }

        $array = $array[$segment];
    }
    return $array;
}