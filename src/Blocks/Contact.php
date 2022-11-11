<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Contact extends Block
{
    use Traits\Flow;

    const NAME = 'content.contact';
    const LABEL = "Contenu: Widget contact";
    const SCREEN_PREVIEW_SIZE = [780, 290];

    public function toArray()
    {
        return [];
    }
}
