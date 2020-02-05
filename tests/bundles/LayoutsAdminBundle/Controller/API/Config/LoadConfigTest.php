<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\App\MockerContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadConfigTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadConfig::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadConfig::__invoke
     */
    public function testLoadConfig(): void
    {
        $clientContainer = $this->client->getContainer();
        if (!$clientContainer instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager */
        $tokenManager = $clientContainer->get('security.csrf.token_manager');
        $tokenId = $clientContainer->getParameter('netgen_layouts.app.csrf_token_id');

        $currentToken = $tokenManager->getToken($tokenId);

        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/config');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode((string) $response->getContent(), true);

        self::assertIsArray($responseContent);

        self::assertArrayHasKey('config', $responseContent);
        self::assertArrayHasKey('layout_types', $responseContent);
        self::assertArrayHasKey('block_types', $responseContent);
        self::assertArrayHasKey('block_type_groups', $responseContent);

        self::assertArrayHasKey('csrf_token', $responseContent['config']);
        self::assertIsString($responseContent['config']['csrf_token']);
        self::assertSame($currentToken->getValue(), $responseContent['config']['csrf_token']);

        self::assertIsArray($responseContent['layout_types']);
        self::assertNotEmpty($responseContent['layout_types']);

        self::assertIsArray($responseContent['block_types']);
        self::assertNotEmpty($responseContent['block_types']);

        self::assertIsArray($responseContent['block_type_groups']);
        self::assertNotEmpty($responseContent['block_type_groups']);
    }
}
