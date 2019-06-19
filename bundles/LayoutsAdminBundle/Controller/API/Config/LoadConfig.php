<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Block\Registry\BlockTypeGroupRegistry;
use Netgen\Layouts\Block\Registry\BlockTypeRegistry;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class LoadConfig extends AbstractController
{
    /**
     * @var \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry
     */
    private $layoutTypeRegistry;

    /**
     * @var \Netgen\Layouts\Block\Registry\BlockTypeRegistry
     */
    private $blockTypeRegistry;

    /**
     * @var \Netgen\Layouts\Block\Registry\BlockTypeGroupRegistry
     */
    private $blockTypeGroupRegistry;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(
        LayoutTypeRegistry $layoutTypeRegistry,
        BlockTypeRegistry $blockTypeRegistry,
        BlockTypeGroupRegistry $blockTypeGroupRegistry,
        ConfigurationInterface $configuration,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->blockTypeGroupRegistry = $blockTypeGroupRegistry;
        $this->configuration = $configuration;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Returns the general config.
     */
    public function __invoke(): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new ArrayValue(
            [
                'layout_types' => $this->getLayoutTypes(),
                'block_types' => $this->getBlockTypes(),
                'block_type_groups' => $this->getBlockTypeGroups(),
                'config' => [
                    'automatic_cache_clear' => $this->configuration->getParameter('app.automatic_cache_clear'),
                    'csrf_token' => $this->csrfTokenManager->getToken(
                        $this->configuration->getParameter('app.csrf_token_id')
                    )->getValue(),
                ],
            ]
        );
    }

    private function getLayoutTypes(): Generator
    {
        foreach ($this->layoutTypeRegistry->getLayoutTypes(true) as $layoutType) {
            yield new View($layoutType);
        }
    }

    private function getBlockTypes(): Generator
    {
        foreach ($this->blockTypeRegistry->getBlockTypes(true) as $blockType) {
            yield new Value($blockType);
        }
    }

    private function getBlockTypeGroups(): Generator
    {
        foreach ($this->blockTypeGroupRegistry->getBlockTypeGroups(true) as $blockTypeGroup) {
            if (count($blockTypeGroup->getBlockTypes(true)) > 0) {
                yield new Value($blockTypeGroup);
            }
        }
    }
}
