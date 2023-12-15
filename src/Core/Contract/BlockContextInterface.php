<?php

namespace Coretik\PageBuilder\Core\Contract;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use Coretik\PageBuilder\Core\Block\Context\BlockContextType;

interface BlockContextInterface
{
    public function getBlock(): ?BlockInterface;
    public function getName(): string;
    public function getCategory(): string;
    public function getData();
    public function getType(): BlockContextType;
    public function toArray(): array;
}
