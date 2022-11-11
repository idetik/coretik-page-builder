<?php

namespace Coretik\PageBuilder\Blocks;

use Coretik\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class WysiwygDouble extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.wysiwyg-double';
    const LABEL = 'Contenu: Éditeur de texte (2 colonnes)';

    protected $content_1;
    protected $content_2;

    public function toArray()
    {
        return [
            'wysiwyg_1' => (new Wysiwyg(['content' => $this->content_1]))->toArray(),
            'wysiwyg_2' => (new Wysiwyg(['content' => $this->content_2]))->toArray(),
        ];
    }
}
