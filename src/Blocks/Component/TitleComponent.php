<?php

namespace Coretik\PageBuilder\Blocks\Component;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TitleComponent extends BlockComponent
{
    const NAME = 'components.title';
    const LABEL = 'Titre';

    protected $title;
    protected $tag;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $field->addText('title')
                ->setLabel(__('Titre', app()->get('settings')['text-domain']))
                ->setRequired()
            ->addRadio('tag')
                ->setLabel(__('Niveau de titre', app()->get('settings')['text-domain']))
                ->addChoice('h2')
                ->addChoice('h3')
                ->addChoice('h4')
                ->addChoice('h5')
                ->setDefaultValue('h2')
                ->setRequired();
        return $field;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'tag' => $this->tag
        ];
    }

    protected function getPlainHtml(): string
    {
        if (\locate_template($this->template())) {
            return parent::getPlainHtml();
        }

        return sprintf('<%1$s>%2$s<%1$s>', $this->tag, $this->title);
    }
}
