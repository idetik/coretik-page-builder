<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;
use Coretik\PageBuilder\Blocks\Traits\Flow;

use function Globalis\WP\Cubi\include_template_part;

class Cta extends Block
{
    use Flow;

    const NAME = 'content.cta';
    const LABEL = 'Call-to-action';

    protected $cta;

    public function toArray()
    {
        return [
            'cta' => $this->cta
        ];
    }
}
