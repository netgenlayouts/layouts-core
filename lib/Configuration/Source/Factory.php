<?php

namespace Netgen\BlockManager\Configuration\Source;

class Factory
{
    /**
     * Builds the source.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source
     */
    public static function buildSource(array $config, $identifier)
    {
        $queries = array();

        foreach ($config['queries'] as $queryIdentifier => $queryConfig) {
            $queries[$queryIdentifier] = new Query(
                $queryIdentifier,
                $config['queries'][$queryIdentifier]['query_type'],
                $config['queries'][$queryIdentifier]['default_parameters']
            );
        }

        return new Source($identifier, $config['name'], $queries);
    }
}
