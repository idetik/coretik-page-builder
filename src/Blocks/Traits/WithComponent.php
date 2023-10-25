<?php

namespace Coretik\PageBuilder\Blocks\Traits;

trait WithComponent
{
    protected function component(mixed $data)
    {
        return $this->config('factory')->create($data);
    }
}
