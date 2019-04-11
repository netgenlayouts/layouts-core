<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class RequestUri implements TargetTypeInterface
{
    public static function getType(): string
    {
        return 'request_uri';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
        ];
    }

    public function provideValue(Request $request)
    {
        return $request->getRequestUri();
    }
}
