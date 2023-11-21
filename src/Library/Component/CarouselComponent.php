<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class CarouselComponent extends BlockComponent
{
    const NAME = 'components.carousel';
    const LABEL = 'Carousel';

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
