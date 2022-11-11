<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Title extends Block
{
    use Traits\Flow;

    // @deprecated since 0.31.5
    use Traits\Background;

    const NAME = 'headings.title';
    const LABEL = 'Titre';

    protected $title;
    protected $position;

    public function toArray()
    {
        return [
            'title' => $this->title,
            'position' => $this->position
        ];
    }
}
