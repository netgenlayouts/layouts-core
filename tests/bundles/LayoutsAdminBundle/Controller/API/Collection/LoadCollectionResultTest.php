<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadCollectionResultTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadCollectionResult::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResult(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/collections/08937ca0-18f4-5806-84df-8c132c36cabe/result');

        $this->assertResponse(
            $this->client->getResponse(),
            'collections/load_collection_result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResultWithNonExistentCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/collections/ffffffff-ffff-ffff-ffff-ffffffffffff/result');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
