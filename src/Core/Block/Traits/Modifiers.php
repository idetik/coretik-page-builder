<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use Coretik\PageBuilder\Core\Block\Modifier\Modifier;
use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Add modifiers callback to the block.
 */
trait Modifiers
{
    protected array $modifiers = [];
    protected array $lockModifier = [];

    public function addModifier(callable $modifier, int $priority = 10): self
    {
        if (!\is_callable($modifier, true, $modifier_name)) {
            return $this;
        }

        if (\in_array($modifier_name, $this->lockModifier)) {
            return $this;
        }

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

    /**
     * Search and remove modifiers already set
     * @param callable $provider The callable to remove
     * @param int $priority The priority wich the callable was registered
     * @param int $lock Lock this modifier to prevent register again
     */
    public function removeModifier(Modifier|callable $provider, int $priority = 10, bool $lock = false): self
    {
        if ($provider instanceof Modifier) {
            $priority = $provider::PRIORITY;
            $provider = [$provider, 'handle'];
        }

        if (!\is_callable($provider, true, $provider_name)) {
            return $this;
        }

        foreach ($this->modifiers[$priority] as $i => $provider) {
            if (\is_callable($provider, true, $callable_name) && $provider_name === $callable_name) {
                unset($this->modifiers[$priority][$i]);
            }
        }

        if ($lock) {
            $this->lockModifier($provider_name);
        }

        return $this;
    }

    /**
     * Prevent modifiers callable to be registered in the future
     */
    public function lockModifier(callable|string $provider): self
    {
        if (\is_string($provider)) {
            $provider_name = $provider;
        } elseif (!\is_callable($provider, true, $provider_name)) {
            return $this;
        }

        $this->lockModifier[] = $provider_name;
        return $this;
    }

    public function removeAllModifiers(): self
    {
        $this->modifiers = [];
        return $this;
    }
}
