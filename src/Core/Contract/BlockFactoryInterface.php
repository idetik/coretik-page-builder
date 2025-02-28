<?php

namespace Coretik\PageBuilder\Core\Contract;

interface BlockFactoryInterface
{
    public function create(string|array $layout, ?BlockContextInterface $context = null): BlockInterface;
}
