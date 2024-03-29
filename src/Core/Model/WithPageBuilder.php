<?php

namespace Coretik\PageBuilder\Core\Model;

trait WithPageBuilder
{
    protected $builder;
    protected bool $builderLoaded = false;

    abstract public function getBlocks(): array;

    public function initializeWithPageBuilder()
    {
        $this->builder = app()->get('pageBuilder');
    }

    protected function loadPageBuilder()
    {
        $blocks = $this->getBlocks();

        if (!is_array($blocks)) {
            $blocks = [];
        }

        $this->builder->setBlocks($blocks, [$this, 'wrapTheBlock']);
        $this->builderLoaded = true;
    }

    public function rewind()
    {
        $this->builder->blocks()->rewind();
    }

    public function builder()
    {
        if (!$this->builderLoaded) {
            $this->loadPageBuilder();
        }

        return $this->builder;
    }

    public function haveBlocks()
    {
        if (!$this->builderLoaded) {
            $this->loadPageBuilder();
        }

        return $this->builder->blocks()->valid();
    }

    public function getTheBlock($return = false)
    {
        $block = $this->builder->blocks()->current();
        $this->builder->blocks()->next();
        return $block->render($return);
    }

    public function wrapTheBlock($block)
    {
        return $block;
    }
}
