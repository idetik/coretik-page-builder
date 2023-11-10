<?php

namespace Coretik\PageBuilder\Library\Block\Editorial;

use Coretik\PageBuilder\Core\Block\BlockComposite;
use Coretik\PageBuilder\Library\Component\WysiwygComponent;

use function Coretik\PageBuilder\Core\Block\Modifier\required;

class WysiwygBlock extends BlockComposite
{
    const NAME = 'editorial.wysiwyg';
    const LABEL = 'Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected function prepareComponents(): array
    {
        return [
            'wysiwyg' => required(WysiwygComponent::class)
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        return $parameters['wysiwyg'] ?? '';
    }
}
