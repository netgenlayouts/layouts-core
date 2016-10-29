<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;

interface QueryTypeInterface
{
    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters();

    /**
     * Returns the values from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(Query $query, $offset = 0, $limit = null);

    /**
     * Returns the value count from the query.
     *
     * To the outside world, query count is whatever the query returns
     * based on parameter values. This may not correspond to inner query count
     * when parameters themselves contain offset and limit parameters which are then
     * used for inner query.
     *
     * Due to that, this method takes the inner query limit (as used in parameters)
     * and returns it instead if returned count is larger.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query);

    /**
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    public function getHandler();

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig();
}
