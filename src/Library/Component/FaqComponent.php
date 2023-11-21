<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class FaqComponent extends BlockComponent
{
    const NAME = 'components.faq';
    const LABEL = 'F.A.Q.';

    protected $content;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        return $field;
    }

    public function toArray()
    {
        return [

        ];
    }
}
