<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Exception;
use Jean85\PrettyVersions;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Twig\Environment;
use Twig\Source;
use Version\Exception\InvalidVersionStringException;
use Version\Version;

final class LayoutsDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Netgen\Layouts\API\Values\Layout\Layout[]
     */
    private $layoutCache = [];

    public function __construct(LayoutService $layoutService, GlobalVariable $globalVariable, Environment $twig)
    {
        $this->layoutService = $layoutService;
        $this->globalVariable = $globalVariable;
        $this->twig = $twig;

        $this->data['version'] = PrettyVersions::getVersion('netgen/layouts-core')->getPrettyVersion();
        $this->data['docs_version'] = 'latest';

        try {
            $version = Version::fromString($this->data['version']);
            $this->data['docs_version'] = sprintf('%d.%d', $version->getMajor(), $version->getMinor());
        } catch (InvalidVersionStringException $e) {
            // Do nothing
        }

        $this->reset();
    }

    public function collect(Request $request, Response $response, ?Exception $exception = null): void
    {
        $rule = $this->globalVariable->getRule();
        $layoutView = $this->globalVariable->getLayoutView();

        if ($rule instanceof Rule) {
            $this->collectRule($rule);
        }

        if ($layoutView instanceof LayoutViewInterface) {
            $this->collectLayout($layoutView);
        } elseif ($layoutView === false) {
            $this->data['layout'] = false;
        }
    }

    public function reset(): void
    {
        $this->data['rule'] = null;
        $this->data['layout'] = null;
        $this->data['blocks'] = [];
    }

    /**
     * Collects the layout data.
     */
    public function collectLayout(LayoutViewInterface $layoutView): void
    {
        $layout = $layoutView->getLayout();

        $this->data['layout'] = [
            'id' => $layout->getId()->toString(),
            'name' => $layout->getName(),
            'type' => $layout->getLayoutType()->getName(),
            'context' => $layoutView->getContext(),
            'template' => null,
            'template_path' => null,
        ];

        if ($layoutView->getTemplate() !== null) {
            $templateSource = $this->getTemplateSource($layoutView->getTemplate());

            $this->data['layout']['template'] = $templateSource->getName();
            $this->data['layout']['template_path'] = $templateSource->getPath();
        }
    }

    /**
     * Collects the rule data.
     */
    public function collectRule(Rule $rule): void
    {
        $this->data['rule'] = [
            'id' => $rule->getId()->toString(),
        ];

        foreach ($rule->getTargets() as $target) {
            $this->data['rule']['targets'][] = [
                'type' => $target->getTargetType()::getType(),
                'value' => $this->cloneVar($target->getValue()),
            ];
        }

        foreach ($rule->getConditions() as $condition) {
            $this->data['rule']['conditions'][] = [
                'type' => $condition->getConditionType()::getType(),
                'value' => $this->cloneVar($condition->getValue()),
            ];
        }
    }

    /**
     * Collects the block view data.
     */
    public function collectBlockView(BlockViewInterface $blockView): void
    {
        $block = $blockView->getBlock();
        $blockDefinition = $block->getDefinition();

        $layoutId = $block->getLayoutId()->toString();
        $this->layoutCache[$layoutId] = $this->layoutCache[$layoutId] ?? $this->layoutService->loadLayout($block->getLayoutId());

        $blockData = [
            'id' => $block->getId()->toString(),
            'name' => $block->getName(),
            'layout_id' => $layoutId,
            'layout_name' => $this->layoutCache[$layoutId]->getName(),
            'definition' => $blockDefinition->getName(),
            'view_type' => $blockDefinition->hasViewType($block->getViewType()) ?
                $blockDefinition->getViewType($block->getViewType())->getName() :
                'Invalid view type',
            'locale' => $block->getLocale(),
            'template' => null,
            'template_path' => null,
        ];

        if ($blockView->getTemplate() !== null) {
            $templateSource = $this->getTemplateSource($blockView->getTemplate());

            $blockData['template'] = $templateSource->getName();
            $blockData['template_path'] = $templateSource->getPath();
        }

        $this->data['blocks'][] = $blockData;
    }

    /**
     * Returns the collected data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getName(): string
    {
        return 'nglayouts';
    }

    private function getTemplateSource(string $name): Source
    {
        return $this->twig->load($name)->getSourceContext();
    }
}