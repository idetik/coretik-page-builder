<?php

namespace Coretik\PageBuilder\Blocks\Content;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;
use Coretik\PageBuilder\Blocks\Traits\{Composite};
use Coretik\PageBuilder\Blocks\Components\Wysiwyg as WysiwygComponent;

use function Globalis\WP\Cubi\include_template_part;

class Wysiwyg extends Block
{
    use Composite;

    const NAME = 'content.wysiwyg';
    const LABEL = 'Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected $components = [
        WysiwygComponent::class ,
    ];

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        foreach ($this->fieldsComposite() as $name => $data) {
            $field->addTab($data['block']->getLabel(), ['placement' => 'left'])
                ->addFields($data['fields']);
        }
        $this->useSettingsOn($field);
        return $field;
    }

    public function toArray()
    {
        return $this->componentToArray(WysiwygComponent::class);
    }
}
