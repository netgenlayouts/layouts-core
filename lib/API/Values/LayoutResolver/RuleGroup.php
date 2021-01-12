<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class RuleGroup implements Value
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
     */
    private $rules;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\LayoutResolver\Condition>
     */
    private $conditions;

    public function __construct()
    {
        $this->rules = $this->rules ?? new ArrayCollection();
        $this->conditions = $this->conditions ?? new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns if the rule group is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns the rule group priority.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Returns the rule group comment.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Returns all the rules in the rule group.
     */
    public function getRules(): RuleList
    {
        return new RuleList($this->rules->toArray());
    }

    /**
     * Returns all conditions in the rule group.
     */
    public function getConditions(): ConditionList
    {
        return new ConditionList($this->conditions->toArray());
    }
}
