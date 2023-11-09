<?php

namespace Coretik\PageBuilder\Library\Block\Editorial;

use Coretik\PageBuilder\Core\Block\BlockComposite;
use Coretik\PageBuilder\Library\Component\WysiwygComponent;

use function Coretik\PageBuilder\Core\Block\Modifier\required;

class WysiwygDoubleBlock extends BlockComposite
{
    const NAME = 'editorial.wysiwyg-double';
    const LABEL = 'Éditeur de texte (2 colonnes)';

    protected function prepareComponents(): array
    {
        return [
            'wysiwyg' => required(WysiwygComponent::class),
            'column_2' => WysiwygComponent::class,
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        return $parameters['wysiwyg'] . $parameters['column_2'];
    }
}
