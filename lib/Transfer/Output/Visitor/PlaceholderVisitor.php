<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Placeholder value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Block\Placeholder
 */
final class PlaceholderVisitor implements VisitorInterface
{
    public function accept($value): bool
    {
        return $value instanceof Placeholder;
    }

    public function visit($placeholder, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder */

        return [
            'identifier' => $placeholder->getIdentifier(),
            'blocks' => $this->visitBlocks($placeholder, $subVisitor),
        ];
    }

    /**
     * Visit the given $placeholder blocks into hash representation.
     */
    private function visitBlocks(Placeholder $placeholder, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($placeholder as $block) {
            $hash[] = $subVisitor->visit($block);
        }

        return $hash;
    }
}