<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use Coretik\PageBuilder\Core\Block\Traits\Components;
use Coretik\PageBuilder\Core\Contract\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;

class RepeatearComponent extends BlockComponent
{
    const NAME = 'component.repeater';
    const LABEL = 'Répéteur';

    use Components;

    protected string $repeater_name = 'rows';
    private array $args = [];
    private BlockInterface $component;

    public function setComponent(BlockInterface $component): self
    {
        $this->component = $component;
        return $this;
    }

    public function setRepeaterName(string $repeater_name): self
    {
        $this->repeater_name = $repeater_name;
        return $this;
    }

    public function setArgs(array $args): self
    {
        $this->args = $args;
        return $this;
    }

    public function setProps(array $props)
    {
        foreach ($props as $key => $value) {
            if ($key === 'repeater_name') {
                $this->repeater_name = $value;
                $this->propsFilled[$key] = $value;
            } elseif (\property_exists($this, $key)) {
                $this->$key = $value;
                $this->propsFilled[$key] = $value;
            }
        }

        if (!empty($this->repeater_name) && is_array($props) && array_key_exists($this->repeater_name, $props)) {
            $this->{$this->repeater_name} = $props[$this->repeater_name];
            $this->propsFilled[$this->repeater_name] = $props[$this->repeater_name];
        }
        return $this;
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $args = \wp_parse_args($this->args, [
            'label' => $this->component->getLabel(),
            'layout' => 'block',
        ]);

        $field = $this->createFieldsBuilder();
        $field
            ->addField('repeater_name', 'acfe_hidden', ['default_value' => $this->repeater_name])
            ->addRepeater($this->repeater_name, $args)
                ->addField('acf_fc_layout', 'acfe_hidden', ['default_value' => $this->component->getName()])
                ->addFields($this->component->fields())
            ->end();
        $this->useSettingsOn($field);
        return $field;
    }

    protected function getPlainHtml(array $parameters): string
    {
        if ($this->templateExists()) {
            return parent::getPlainHtml($parameters);
        }

        return implode('', $parameters[$this->repeater_name]);
    }

    public function toArray()
    {
        return [
            'repeater_name' => $this->repeater_name,
            $this->repeater_name => collect($this->{$this->repeater_name})->map(
                fn ($component) => $this->component($component)->render(true)
            )->all()
        ];
    }
}
