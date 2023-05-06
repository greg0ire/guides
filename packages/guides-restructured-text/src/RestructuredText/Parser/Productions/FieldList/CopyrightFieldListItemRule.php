<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\FieldList;

use phpDocumentor\Guides\Nodes\FieldLists\FieldListItemNode;
use phpDocumentor\Guides\Nodes\Metadata\CopyrightNode;
use phpDocumentor\Guides\Nodes\Metadata\MetadataNode;

use function strtolower;

class CopyrightFieldListItemRule implements FieldListItemRule
{
    public function applies(FieldListItemNode $fieldListItemNode): bool
    {
        return strtolower($fieldListItemNode->getTerm()) === 'copyright';
    }

    public function apply(FieldListItemNode $fieldListItemNode): MetadataNode
    {
        return new CopyrightNode($fieldListItemNode->getPlaintextContent());
    }
}