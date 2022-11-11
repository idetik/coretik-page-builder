<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class IconWysiwyg extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.icon-wysiwyg';
    const LABEL = 'Contenu: Icone & Éditeur de texte';

    protected $icon;
    protected $icon_title;
    protected $wysiwyg;
    protected $reverse;

    public function toArray()
    {
        return [
            'wysiwyg' => $this->wysiwyg,
            'reverse' => $this->reverse,
            'icon' => $this->icon,
            'icon_title' => $this->icon_title,
        ];
    }
}
