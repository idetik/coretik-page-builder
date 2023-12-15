<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use Coretik\PageBuilder\Core\Contract\BlockInterface;

trait Components
{
    protected function component(mixed $data): BlockInterface
    {
        return $this->config('factory')->create($data);
    }
}
