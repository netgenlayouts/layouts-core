<?php

namespace Netgen\BlockManager\View\TemplateProvider;

use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\BlockView;
use InvalidArgumentException;

class BlockViewTemplateProvider implements ViewTemplateProvider
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * Provides a template to the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \InvalidArgumentException If there's no template defined for specified view
     */
    public function provideTemplate(ViewInterface $view)
    {
        if (!$view instanceof BlockView) {
            return;
        }

        $block = $view->getBlock();
        $definitionIdentifier = $block->getDefinitionIdentifier();
        $viewType = $block->getViewType();
        $context = $view->getContext();

        if (empty($this->config[$definitionIdentifier]['templates'][$viewType][$context])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No template could be found for block with "%s" identifier and "%s" view.',
                    $definitionIdentifier,
                    $viewType
                )
            );
        }

        $view->setTemplate(
            $this->config[$definitionIdentifier]['templates'][$viewType][$context]
        );
    }
}
