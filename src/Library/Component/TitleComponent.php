<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use Coretik\PageBuilder\Core\Block\Traits\Customizable;
use Coretik\PageBuilder\Library\Settings\AnchorSettings;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TitleComponent extends BlockComponent
{
    const NAME = 'component.title';
    const LABEL = 'Titre';

    use AnchorSettings;
    use Customizable;

    protected $title;
    protected $tag;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        $field
            ->addText('title')
                ->setLabel(__('Titre', app()->get('settings')['text-domain']))
                ->setRequired()
            ->addRadio('tag', ['layout' => 'horizontal'])
                ->setLabel(__('Niveau de titre', app()->get('settings')['text-domain']))
                ->addChoice('h2')
                ->addChoice('h3')
                ->addChoice('h4')
                ->addChoice('h5')
                ->setDefaultValue('h2')
                ->setRequired();

        $this->useSettingsOn($field);
        return $field;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'tag' => $this->tag
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        if (\locate_template($this->template())) {
            return parent::getPlainHtml($parameters);
        }

        $css = $this->getCssClasses();
        return sprintf(
            '<%1$s id="%3$s" %4$s>%2$s</%1$s>',
            $parameters['tag'],
            $parameters['title'],
            !empty($parameters['anchor']) ? $parameters['anchor']['attr_id'] : $parameters['uniqId'],
            !empty($css) ? ' class="' . $css . '"' : null
        );
    }
}
