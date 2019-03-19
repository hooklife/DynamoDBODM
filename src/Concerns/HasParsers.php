<?php

namespace Hoooklife\DynamodbPodm\Concerns;

use Hoooklife\DynamodbPodm\Parsers\ExpressionAttributeNames;
use Hoooklife\DynamodbPodm\Parsers\ExpressionAttributeValues;
use Hoooklife\DynamodbPodm\Parsers\FilterExpression;
use Hoooklife\DynamodbPodm\Parsers\KeyConditionExpression;
use Hoooklife\DynamodbPodm\Parsers\Placeholder;
use Hoooklife\DynamodbPodm\Parsers\ProjectionExpression;
use Hoooklife\DynamodbPodm\Parsers\UpdateExpression;

trait HasParsers
{
    /**
     * @var FilterExpression
     */
    protected $filterExpression;

    /**
     * @var KeyConditionExpression
     */
    protected $keyConditionExpression;

    /**
     * @var ProjectionExpression
     */
    protected $projectionExpression;

    /**
     * @var UpdateExpression
     */
    protected $updateExpression;

    /**
     * @var ExpressionAttributeNames
     */
    protected $expressionAttributeNames;

    /**
     * @var ExpressionAttributeValues
     */
    protected $expressionAttributeValues;

    /**
     * @var Placeholder
     */
    protected $placeholder;

    public function setupExpressions()
    {
        $this->placeholder = new Placeholder();

        $this->expressionAttributeNames = new ExpressionAttributeNames();

        $this->expressionAttributeValues = new ExpressionAttributeValues();

        $this->keyConditionExpression = new KeyConditionExpression(
            $this->placeholder,
            $this->expressionAttributeValues,
            $this->expressionAttributeNames
        );

        $this->filterExpression = new FilterExpression(
            $this->placeholder,
            $this->expressionAttributeValues,
            $this->expressionAttributeNames
        );

        $this->projectionExpression = new ProjectionExpression($this->expressionAttributeNames);

        $this->updateExpression = new UpdateExpression($this->expressionAttributeNames);
    }

    public function resetExpressions()
    {
        $this->filterExpression->reset();
        $this->keyConditionExpression->reset();
        $this->updateExpression->reset();
    }
}
