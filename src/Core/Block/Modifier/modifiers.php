<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;

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

function tap(BlockInterface|string $block, callable $callback): BlockInterface
{
    return TapModifier::modify($block, $callback);
}

function repeat(BlockInterface|string $block, null|string|array $nameOrArgs = null): BlockInterface
{
    return RepeatModifier::modify($block, $nameOrArgs);
}

function acfml(BlockInterface|string $block): BlockInterface
{
    return AcfmlModifier::modify($block);
}
