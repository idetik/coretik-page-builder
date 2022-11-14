<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Cta extends Component
{
    const NAME = 'components.cta';
    const LABEL = 'Call-to-action';

    protected $cta;

    public function toArray()
    {
        return [
            'cta' => $this->cta
        ];
    }
}
