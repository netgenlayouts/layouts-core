<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
final class RuleList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Rule[] $rules
     */
    public function __construct(array $rules = [])
    {
        parent::__construct(
            array_filter(
                $rules,
                static function (Rule $rule): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Rule[]
     */
    public function getRules(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getRuleIds(): array
    {
        return array_map(
            static function (Rule $rule): UuidInterface {
                return $rule->getId();
            },
            $this->getRules()
        );
    }
}
