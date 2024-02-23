<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TextComponent extends BlockComponent
{
    const NAME = 'component.text';
    const LABEL = 'Texte';

    protected $text;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        $field->addText('text')
            ->setLabel(__('Texte', app()->get('settings')['text-domain']));
        return $field;
    }

    protected function getPlainHtml(array $parameters): string
    {
        if (\locate_template($this->template())) {
            return parent::getPlainHtml($parameters);
        }

        return $this->text;
    }

    public function toArray()
    {
        return [
            'text' => $this->text
        ];
    }
}
