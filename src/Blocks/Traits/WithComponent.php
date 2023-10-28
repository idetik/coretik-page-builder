<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use Coretik\PageBuilder\BlockInterface;

trait WithComponent
{
    protected function component(mixed $data): BlockInterface
    {
        return $this->config('factory')->create($data);
    }
}
