<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

trait Customizable
{
    private array $cssClasses = [];

    public function withCssClasses(string|array $cssClasses): self
    {
        if (is_string($cssClasses)) {
            $cssClasses = explode(' ', $cssClasses);
        }

        $this->cssClasses = array_merge($this->cssClasses, $cssClasses);
        return $this;
    }

    public function getCssClasses(): string
    {
        return \apply_filters('coretik/page-builder/block/customizable/css', implode(' ', $this->cssClasses));
    }
}
