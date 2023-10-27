<?php

namespace Coretik\PageBuilder\Blocks\Content;

use Coretik\PageBuilder\Blocks\BlockComposite;
use Coretik\PageBuilder\Blocks\Components\WysiwygComponent;

class WysiwygDoubleBlock extends BlockComposite
{
    const NAME = 'content.wysiwyg-double';
    const LABEL = 'Contenu: Éditeur de texte (2 colonnes)';

    protected $components = [
        'column_1' => WysiwygComponent::class,
        'column_2' => WysiwygComponent::class,
    ];
}
