<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TrueFalseComponent extends BlockComponent
{
    const NAME = 'component.true-false';
    const LABEL = 'True/False';

    protected $trueLabel;
    protected $falseLabel;
    protected $selfLabel = 'True/False';
    protected $boolean;

    public static function labels(string $trueLabel, string $falseLabel): self
    {
        $instance = new self();
        $instance->setTrueLabel($trueLabel);
        $instance->setFalseLabel($falseLabel);
        return $instance;
    }

    public static function named(string $name): self
    {
        $instance = new self();
        $instance->setTitle($name);
        return $instance;
    }

    public function setTrueLabel(string $label): self
    {
        $this->trueLabel = $label;
        return $this;
    }

    public function setFalseLabel(string $label): self
    {
        $this->falseLabel = $label;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->selfLabel = $title;
        return $this;
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        $field->addTrueFalse('boolean', [
            'ui' => 1,
            'ui_on_text' => $this->trueLabel ?: 'Yes',
            'ui_off_text' => $this->falseLabel ?: 'No',
        ])->setLabel($this->selfLabel);
        return $field;
    }

    protected function getPlainHtml(array $parameters): string
    {
        if (\locate_template($this->template())) {
            return parent::getPlainHtml($parameters);
        }

        return '';
    }

    public function toArray()
    {
        return [
            'boolean' => $this->boolean
        ];
    }
}
