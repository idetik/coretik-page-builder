<?php

namespace Coretik\PageBuilder\Blocks\Block\Editorial;

use Coretik\PageBuilder\Blocks\BlockComposite;
use Coretik\PageBuilder\Blocks\Component\WysiwygComponent;

use function Coretik\PageBuilder\Blocks\Modifier\required;

class WysiwygDoubleBlock extends BlockComposite
{
    const NAME = 'content.wysiwyg-double';
    const LABEL = 'Éditeur de texte (2 colonnes)';

    protected $components = [
        'column_1' => WysiwygComponent::class,
        'column_2' => WysiwygComponent::class,
    ];
}
