<?php

namespace Coretik\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;

use function Globalis\WP\Cubi\include_template_part;

class Cta extends Block
{
    use Traits\Flow;

    const NAME = 'content.cta';
    const LABEL = 'Contenu: Call-to-action';

    protected $cta;

    public function toArray()
    {
        return [
            'cta' => $this->cta
        ];
    }
}
