<?php

namespace Coretik\PageBuilder;

class Builder
{
    protected $context = null;
    protected $builder_blocks;
    protected $config;
    private $factory;

    public function __construct($blockFactory, $config = [])
    {
        $this->builder_blocks = new \SplObjectStorage();
        $this->factory = $blockFactory;
        $this->config = $config;
    }

    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    public function setBlocks(array $blocks, callable $wrapAction = null)
    {
        foreach ($blocks as $block) {
            $block = $this->factory->create($block, $this->context);
            if (!empty($wrapAction)) {
                $block = \call_user_func($wrapAction, $block, $this);
            }
            $this->blocks()->attach($block);
        }
    }

    public function factory()
    {
        return $this->factory;
    }

    public function blocks()
    {
        return $this->builder_blocks;
    }

    public function reset()
    {
        $this->builder_blocks = new \SplObjectStorage();
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

    public function library()
    {
        $blocks = $this->config['blocks']
            ->filter(fn ($block) => $block::IN_LIBRARY)
            ->map(fn ($block) => $block::NAME)
            ->all();

        return \apply_filters('coretik/page-builder/library', $blocks);
    }
}
