<?php

namespace Coretik\PageBuilder\Blocks\Component;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TextComponent extends BlockComponent
{
    const NAME = 'components.text';
    const LABEL = 'Texte';

    protected $text;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $field->addWysiwyg('text')
            ->setLabel(__('Texte', app()->get('settings')['text-domain']));
        return $field;
    }

    public function toArray()
    {
        return [
            'wysiwyg' => $this->content
        ];
    }
}
