<?php

namespace Coretik\PageBuilder\Blocks\Block\Editorial;

use Coretik\PageBuilder\Blocks\BlockComposite;
use Coretik\PageBuilder\Blocks\Component\WysiwygComponent;

class WysiwygBlock extends BlockComposite
{
    const NAME = 'editorial.wysiwyg';
    const LABEL = 'Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected $components = [
        'wysiwyg' => WysiwygComponent::class,
    ];

    protected function getPlainHtml(array $parameters): string
    {
        return $parameters['wysiwyg'];
    }
}
