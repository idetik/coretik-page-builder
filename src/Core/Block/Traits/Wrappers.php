<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

/**
 * Add wrappers to the block html output.
 */
trait Wrappers
{
    private array $wrappers = [];
    protected array $wrapperParameters = [];

    public function addWrapper(callable $wrapper, int $priority = 10): self
    {
        $this->wrappers[$priority][] = $wrapper;
        return $this;
    }

    public function wrapperParameters(): array
    {
        $parameters = [];
        foreach ($this->wrapperParameters as $key) {
            $parameters[$key] = $this->$key ?? null;
        }
        return $parameters;
    }

    protected function applyWrappers(string $output): string
    {
        \ksort($this->wrappers, SORT_NUMERIC);
        foreach ($this->wrappers as $priority => $callables) {
            foreach ($callables as $callable) {
                $output = \call_user_func($callable, $output, $this);
            }
        }

        return $output;
    }
}
