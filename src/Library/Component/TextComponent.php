<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TextComponent extends BlockComponent
{
    const NAME = 'components.text';
    const LABEL = 'Texte';

    protected $text;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        $field->addText('text')
            ->setLabel(__('Texte', app()->get('settings')['text-domain']));
        return $field;
    }

    public function toArray()
    {
        return [
            'text' => $this->text
        ];
    }
}
