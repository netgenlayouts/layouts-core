<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Collection\Result\ResultSet;

final class LoadCollectionResult extends AbstractController
{
    /**
     * @var \Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    public function __construct(PagerFactory $pagerFactory)
    {
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * Returns the collection result.
     */
    public function __invoke(Collection $collection): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        // In non AJAX scenarios, we're always rendering the first page of the collection
        // as specified by offset and limit in the collection itself
        $pager = $this->pagerFactory->getPager($collection, 1, null, ResultSet::INCLUDE_ALL_ITEMS);

        return new Value($pager->getCurrentPageResults());
    }
}
