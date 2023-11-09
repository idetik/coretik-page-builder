<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Add modifiers callback to the block.
 */
trait Modifiers
{
    protected array $modifiers = [];

    public function addModifier(callable $modifier, int $priority = 10): self
    {
        $this->modifiers[$priority][] = $modifier;
        return $this;
    }

    protected function applyModifiers(FieldsBuilder $fields): FieldsBuilder
    {
        \ksort($this->modifiers, SORT_NUMERIC);
        foreach ($this->modifiers as $priority => $callables) {
            foreach ($callables as $callable) {
                $fields = \call_user_func($callable, $fields, $this);
            }
        }
        return $fields;
    }
}
