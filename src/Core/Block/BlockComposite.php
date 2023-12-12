<?php

namespace Coretik\PageBuilder\Core\Block;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Core\Block\Traits\Composite;

abstract class BlockComposite extends Block
{
    const AUTO_BUILD_FIELDS = true;
    const RENDER_COMPONENTS = RenderingType::Html;

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

        $tabs = [];

        $field = $this->createFieldsBuilder();
        foreach ($this->fieldsComposite() as $name => $data) {
            $tabName = \apply_filters('coretik/page-builder/composite/tab-name', $name, $tabs, $data, $this);

            $i = 0;
            while (in_array($tabName, $tabs)) {
                $i++;
                $tabName = sprintf('%s %s', $tabName, $i);
            }
            $tabs[] = $tabName;

            $field->addTab($tabName, ['placement' => 'left'])
                ->addFields($data['fields']);
        }

        $this->useSettingsOn($field);

        return $field;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->components as $key => $component) {
            if (\is_int($key)) {
                $key = $component;
            }

            $data[(string)$key] = match(static::RENDER_COMPONENTS) {
                RenderingType::Html => $this->renderComponent($key),
                RenderingType::Array => $this->componentToArray($key),
                RenderingType::Object => $this->resolveComponent($key),
            };
        }

        return $data;
    }
}
