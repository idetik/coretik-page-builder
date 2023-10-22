<?php

namespace Coretik\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Traits\Composite;


abstract class BlockComposite extends Block
{
    const AUTO_BUILD_FIELDS = true;
    const RENDER_COMPONENTS = true;

    use Composite;

    /**
     * Compose layouts with components or others blocks
     * [
     *      'key' => Block::class
     * ]
     */
    protected $components = [];

    public function fieldsBuilder(): FieldsBuilder
    {
        if (!static::AUTO_BUILD_FIELDS) {
            return parent::fieldsBuilder();
        }

        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        foreach ($this->fieldsComposite() as $name => $data) {
            $field->addTab($data['block']->getLabel(), ['placement' => 'left'])
                ->addFields($data['fields']);
        }
        $this->useSettingsOn($field);

        return $field;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->components as $key => $component) {
            $data[(string)$key] = static::RENDER_COMPONENTS
                ? $this->renderComponent($component)
                : $this->componentToArray($component);
        }

        return $data;
    }
}
