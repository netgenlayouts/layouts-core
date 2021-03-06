<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class HelpersExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_layout_name',
                [HelpersRuntime::class, 'getLayoutName']
            ),
            new TwigFunction(
                'nglayouts_value_type_name',
                [HelpersRuntime::class, 'getValueTypeName']
            ),
            new TwigFunction(
                'nglayouts_format_datetime',
                [HelpersRuntime::class, 'formatDateTime'],
                [
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'nglayouts_locale_name',
                [HelpersRuntime::class, 'getLocaleName']
            ),
            new TwigFilter(
                'nglayouts_country_flag',
                [HelpersRuntime::class, 'getCountryFlag']
            ),
            new TwigFilter(
                'nglayouts_format_datetime',
                [HelpersRuntime::class, 'formatDateTime'],
                [
                    'needs_environment' => true,
                ]
            ),
        ];
    }
}
