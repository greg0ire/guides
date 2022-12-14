<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

use function trim;

/**
 * Renders a code block, example:
 *
 * .. code-block:: php
 *
 *      <?php
 *
 *      echo "Hello world!\n";
 *
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/directives.html#directive-code-block
 */
class CodeBlock extends Directive
{
    public function getName(): string
    {
        return 'code-block';
    }

    /**
     * @param string[] $options
     */
    public function process(
        MarkupLanguageParser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ): ?Node {
        if ($node === null) {
            return null;
        }

        if ($node instanceof CodeNode) {
            $node->setLanguage(trim($data));
            $this->setStartingLineNumberBasedOnOptions($options, $node);
        }

        $document = $parser->getDocument();
        if ($variable !== '') {
            $document->addVariable($variable, $node);
            return null;
        }

        return $node;
    }

    /**
     * @param string[] $options
     */
    private function setStartingLineNumberBasedOnOptions(array $options, CodeNode $node): void
    {
        $startingLineNumber = null;
        if (isset($options['linenos'])) {
            $startingLineNumber = 1;
        }

        $startingLineNumber = $options['number-lines'] ?? $options['lineno-start'] ?? $startingLineNumber;

        if ($startingLineNumber === null) {
            return;
        }

        $node->setStartingLineNumber((int) $startingLineNumber);
    }
}
