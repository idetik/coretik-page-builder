<?php

namespace Coretik\PageBuilder\Core\Block;

use Coretik\Core\Utils\Arr;
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

    public function setProps(array $props)
    {
        foreach ($props as $key => $value) {

            if (\property_exists($this, $key)) {
                
                $filled = false;
                foreach ($this->components ?? [] as $compositeKey => $componentClass) {

                    $componentKey = $compositeKey;

                    if (\is_int($compositeKey)) {
                        $componentKey = static::undot($componentClass::NAME);
                    }

                    if ($componentKey === $key) {
                        $this->$key = array_merge(['acf_fc_layout' => $componentClass::NAME], Arr::wrap($value));
                        $this->propsFilled[$key] = array_merge(['acf_fc_layout' => $componentClass::NAME], Arr::wrap($value));
                        $filled = true;
                    }
                }

                if (!$filled) {
                    $this->$key = $value;
                    $this->propsFilled[$key] = $value;
                }
            }

        }
        return $this;
    }

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

        if (empty($this->settings)) {
            return $field;
        }

        $tabSettings = $this->fieldSettingsName() . '_tab';
        if (!$field->fieldExists($tabSettings)) {
            $field
                ->addTab($this->fieldSettingsName(), ['label' => __('Paramètres du bloc ', app()->get('settings')['text-domain']), 'placement' => 'left'])
                ->endpoint()
                ->setAttr('class', 'settings-tab--composite');
        } else {
            $field->getField($tabSettings);
        }

        $this->applySettings($field);

        return $field;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->components as $key => $component) {
            if (\is_int($key)) {
                $key = $component;
            }

            $data[(string)$key] = match (static::RENDER_COMPONENTS) {
                RenderingType::Html => $this->renderComponent($key),
                RenderingType::Array => $this->componentToArray($key),
                RenderingType::Object => $this->resolveComponent($key),
            };
        }

        return $data;
    }
}
