<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Twig\Error\SyntaxError;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class RenderZone extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        $context = null;
        $zone = $this->parser->getExpressionParser()->parseExpression();

        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test(Token::NAME_TYPE, 'context')) {
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $context = $this->parser->getExpressionParser()->parseExpression();

                continue;
            }

            $token = $stream->getCurrent();
            throw new SyntaxError(
                sprintf(
                    'Unexpected token "%s" of value "%s".',
                    Token::typeToEnglish($token->getType()),
                    $token->getValue()
                ),
                $token->getLine(),
                $stream->getSourceContext()->getName()
            );
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new RenderZoneNode($zone, $context, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'ngbm_render_zone';
    }
}
