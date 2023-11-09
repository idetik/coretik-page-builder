<?php

namespace Coretik\PageBuilder\Blocks\Block\Editorial;

use Coretik\PageBuilder\Blocks\BlockComposite;
use Coretik\PageBuilder\Blocks\Component\WysiwygComponent;

use function Coretik\PageBuilder\Blocks\Modifier\required;

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
