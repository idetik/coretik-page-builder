<?php

namespace Coretik\PageBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Builder
{
    protected $context = null;
    protected $builder_blocks;
    protected $config;
    private $factory;

    public function __construct($blockFactory, array $config = [])
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

            if ($block->useGrid()) {
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

    public static function availablesBlocks()
    {
        $dir = get_parent_theme_file_path($this->config['fields.directory']);
        $layouts_default = [];
        foreach (glob($dir . '**/*.php') as $blockfile) {
            $from_blockdir = str_replace($dir, '', $blockfile);
            $layouts_default[] = str_replace(['/', '.php'], ['.', ''], $from_blockdir);
        }

        return \apply_filters('coretik/page-builder/blocks', $layouts_default);
    }
}
