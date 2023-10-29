<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;

function required(BlockInterface|string $block): BlockInterface
{
    return RequiredModifier::modify($block);
}

function tabless(BlockInterface|string $block): BlockInterface
{
    return TablessModifier::modify($block);
}

function tabtop(BlockInterface|string $block): BlockInterface
{
    return TabTopModifier::modify($block);
}

function column(BlockInterface|string $block, int $size = 6): BlockInterface
{
    return ColumnModifier::modify($block, $size);
}
