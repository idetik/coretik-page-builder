<?php

namespace Coretik\PageBuilder\Blocks\Content;

use Coretik\PageBuilder\Blocks\BlockComposite;
use Coretik\PageBuilder\Blocks\Components\WysiwygComponent;

class WysiwygBlock extends BlockComposite
{
    const NAME = 'editorial.wysiwyg';
    const LABEL = 'Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected $components = [
        'wysiwyg' => WysiwygComponent::class,
    ];
}
