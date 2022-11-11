<?php

namespace Themetik\Services\PageBuilder\Blocks\Content;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Themetik\Services\PageBuilder\Blocks\Block;
use Themetik\Services\PageBuilder\Blocks\Traits\Flow;

use function Globalis\WP\Cubi\include_template_part;

class Partners extends Block
{
    use Flow;

    const NAME = 'content.partners';
    const LABEL = 'Partenaires';

    protected $partners;
    protected $per_row;

    public function toArray()
    {
        return [
            'partners' => $this->partners,
            'per_row' => $this->per_row,
        ];
    }
}
