<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use Coretik\PageBuilder\BlockInterface;

trait Components
{
    protected function component(mixed $data): BlockInterface
    {
        return $this->config('factory')->create($data);
    }
}
