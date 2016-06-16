<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Netgen\BlockManager\Persistence\Values\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutUpdateStruct;
use Doctrine\DBAL\Types\Type;

class LayoutQueryHandler extends QueryHandler
{
    /**
     * Loads all data for layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutData($layoutId, $status = null)
    {
        $query = $this->getLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all zone data with provided identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @return array
     */
    public function loadZoneData($layoutId, $identifier, $status = null)
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('layout_id', ':layout_id'),
                $query->expr()->eq('identifier', ':identifier')
            )
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER)
        ->setParameter('identifier', $identifier, Type::STRING);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all data for zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutZonesData($layoutId, $status = null)
    {
        $query = $this->getZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        $query->addOrderBy('identifier', 'ASC');

        return $query->execute()->fetchAll();
    }

    /**
     * Loads all layout collections data.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return array
     */
    public function loadLayoutCollectionsData($layoutId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('bc.block_id', 'bc.block_status', 'bc.identifier', 'bc.collection_id', 'bc.collection_status')
            ->from('ngbm_block_collection', 'bc')
            ->innerJoin('bc', 'ngbm_block', 'b', 'bc.block_id = b.id and bc.block_status = b.status')
            ->where(
                $query->expr()->eq('b.layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status, 'bc.block_status');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns if layout exists.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutExists($layoutId, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if the zone exists.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @return bool
     */
    public function zoneExists($layoutId, $identifier, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('identifier', ':identifier'),
                    $query->expr()->eq('layout_id', ':layout_id')
                )
            )
            ->setParameter('identifier', $identifier, Type::STRING)
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if the layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('name', ':name')
                )
            )
            ->setParameter('name', trim($name), Type::STRING);

        if ($excludedLayoutId !== null) {
            $query->andWhere($query->expr()->neq('id', ':layout_id'))
                ->setParameter('layout_id', $excludedLayoutId, Type::INTEGER);
        }

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutCreateStruct $layoutCreateStruct
     * @param int|string $layoutId
     *
     * @return int
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, $layoutId = null)
    {
        $currentTimeStamp = time();

        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'type' => ':type',
                    'name' => ':name',
                    'created' => ':created',
                    'modified' => ':modified',
                )
            )
            ->setValue(
                'id',
                $layoutId !== null ? (int)$layoutId : $this->connectionHelper->getAutoIncrementValue('ngbm_layout')
            )
            ->setParameter('status', $layoutCreateStruct->status, Type::INTEGER)
            ->setParameter('type', $layoutCreateStruct->type, Type::STRING)
            ->setParameter('name', trim($layoutCreateStruct->name), Type::STRING)
            ->setParameter('created', $currentTimeStamp, Type::INTEGER)
            ->setParameter('modified', $currentTimeStamp, Type::INTEGER);

        $query->execute();

        $createdLayoutId = $layoutId;
        if ($createdLayoutId === null) {
            $createdLayoutId = (int)$this->connectionHelper->lastInsertId('ngbm_layout');
        }

        foreach ($layoutCreateStruct->zoneIdentifiers as $zoneIdentifier) {
            $query = $this->connection->createQueryBuilder()
                ->insert('ngbm_zone')
                ->values(
                    array(
                        'identifier' => ':identifier',
                        'layout_id' => ':layout_id',
                        'status' => ':status',
                    )
                )
                ->setParameter('identifier', $zoneIdentifier, Type::STRING)
                ->setParameter('layout_id', $createdLayoutId, Type::INTEGER)
                ->setParameter('status', $layoutCreateStruct->status, Type::INTEGER);

            $query->execute();
        }

        return $createdLayoutId;
    }

    /**
     * Updates a layout.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\LayoutUpdateStruct $layoutUpdateStruct
     */
    public function updateLayout($layoutId, $status, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('name', ':name')
            ->set('modified', ':modified')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER)
            ->setParameter('name', $layoutUpdateStruct->name, Type::STRING)
            ->setParameter('modified', time(), Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Deletes all layout blocks.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutBlocks($layoutId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes the layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // Delete all zones

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete the layout itself

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Builds and returns a layout database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getLayoutSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT id', 'status', 'type', 'name', 'created', 'modified')
            ->from('ngbm_layout');

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getZoneSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT identifier', 'layout_id', 'status')
            ->from('ngbm_zone');

        return $query;
    }
}
