<?php

namespace Coretik\PageBuilder\Blocks;

use Coretik\PageBuilder\BlockContextType;
use Coretik\PageBuilder\BlockInterface;

class ParentContext extends BlockContext
{
    public function __construct(
        ?BlockInterface $block = null,
        ?string $name = null,
        ?string $category = null,
        mixed $data = null,
        )
    {
        parent::__construct($block, $name, $category, $data);
        $this->setType(BlockContextType::PARENT);
    }
}
