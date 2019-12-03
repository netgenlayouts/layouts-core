<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Condition>
 */
final class ConditionList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Condition[] $conditions
     */
    public function __construct(array $conditions = [])
    {
        parent::__construct(
            array_filter(
                $conditions,
                static function (Condition $condition): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getConditionIds(): array
    {
        return array_map(
            static function (Condition $condition): UuidInterface {
                return $condition->getId();
            },
            $this->getConditions()
        );
    }
}
