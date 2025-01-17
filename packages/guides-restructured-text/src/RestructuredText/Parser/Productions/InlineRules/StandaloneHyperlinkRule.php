<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\InlineRules;

use phpDocumentor\Guides\Nodes\Inline\HyperLinkNode;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\Parser\InlineLexer;

/**
 * Rule to parse for simple anonymous references, such as `myref__`
 */
class StandaloneHyperlinkRule extends ReferenceRule
{
    public function applies(InlineLexer $lexer): bool
    {
        return $lexer->token?->type === InlineLexer::HYPERLINK;
    }

    public function apply(ParserContext $parserContext, InlineLexer $lexer): HyperLinkNode|null
    {
        $node = $this->createReference(
            $parserContext,
            $lexer->token?->value ?? '',
            null,
            false,
        );
        $lexer->moveNext();

        return $node;
    }

    public function getPriority(): int
    {
        return 100;
    }
}
