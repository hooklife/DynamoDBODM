<?php

namespace Hoooklife\DynamodbPodm\Parsers;

use Hoooklife\DynamodbPodm\ComparisonOperator;

class KeyConditionExpression extends ConditionExpression
{
    protected function getSupportedOperators()
    {
        return array_only(static::OPERATORS, [
            ComparisonOperator::EQ,
            ComparisonOperator::LE,
            ComparisonOperator::LT,
            ComparisonOperator::GE,
            ComparisonOperator::GT,
            ComparisonOperator::BEGINS_WITH,
            ComparisonOperator::BETWEEN,
        ]);
    }
}
