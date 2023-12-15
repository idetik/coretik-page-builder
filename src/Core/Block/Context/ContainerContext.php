<?php

namespace Coretik\PageBuilder\Core\Block\Context;

use Coretik\PageBuilder\Core\Block\Context\BlockContextType;
use Coretik\PageBuilder\Core\Contract\BlockInterface;

class ContainerContext extends BlockContext
{
    public function __construct(
        ?BlockInterface $block = null,
        ?string $name = null,
        ?string $category = null,
        mixed $data = null,
    ) {
        parent::__construct($block, $name, $category, $data);
        $this->setType(BlockContextType::CONTAINER);
    }
}
