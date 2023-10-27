<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use Coretik\PageBuilder\Blocks\Modifier\RequiredModifier;

function required(BlockInterface|string $block): BlockInterface
{
    return RequiredModifier::modify($block);
}

