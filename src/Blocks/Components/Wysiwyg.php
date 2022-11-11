<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Wysiwyg extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'components.wysiwyg';
    const LABEL = 'Contenu: Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected $content;

    public function toArray()
    {
        return [
            'wysiwyg' => $this->content
        ];
    }
}
