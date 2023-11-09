<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Core\Contract\BlockContextInterface;
use Coretik\PageBuilder\Core\Contract\BlockFactoryInterface;
use SplObjectStorage;
use ArrayAccess;

class Builder
{
    protected ?BlockContextInterface $context = null;
    protected SplObjectStorage $builderBlocks;
    protected ArrayAccess $config;
    private BlockFactoryInterface $factory;

    public function __construct(BlockFactoryInterface $blockFactory, ?ArrayAccess $config = null)
    {
        $this->builderBlocks = new SplObjectStorage();
        $this->factory = $blockFactory;
        $this->config = $config ?? \collect([]);
    }

    public function setContext(BlockContextInterface $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function setBlocks(array $blocks, callable $wrapAction = null): self
    {
        foreach ($blocks as $block) {
            $block = $this->factory->create($block, $this->context);
            if (!empty($wrapAction)) {
                $block = \call_user_func($wrapAction, $block, $this);
            }
            $this->blocks()->attach($block);
        }
        return $this;
    }

    public function factory(): BlockFactoryInterface
    {
        return $this->factory;
    }

    public function blocks(): SplObjectStorage
    {
        return $this->builderBlocks;
    }

    public function reset(): self
    {
        $this->builderBlocks = new \SplObjectStorage();
        return $this;
    }

    public function hasGridEnabled(): bool
    {
        $gridFounded = false;

        while ($this->blocks()->valid() && !$gridFounded) {
            $block = $this->blocks()->current();

            if (\method_exists($block, 'useGrid') && $block->useGrid()) {
                $gridFounded = true;
            }

            $this->blocks()->next();
        }

        $this->blocks()->rewind();

        return $gridFounded;
    }

    public function setGrid()
    {
        while ($this->blocks()->valid()) {
            $block = $this->blocks()->current();
            $block->setGrid();
            $this->blocks()->next();
        }

        $this->blocks()->rewind();
    }

    public function library(): array
    {
        $blocks = $this->config['blocks']
            ->filter(fn ($block) => $block::IN_LIBRARY)
            ->map(fn ($block) => $block::NAME)
            ->all();

        return \apply_filters('coretik/page-builder/library', $blocks);
    }
}
