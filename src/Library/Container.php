<?php

namespace Coretik\PageBuilder\Library;

use Coretik\PageBuilder\Core\Block\Block;
use Coretik\PageBuilder\Core\Block\ContainerContext;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Container extends Block
{
    const NAME = 'containers.container';
    const LABEL = 'Conteneur';
    const SCREENSHOTABLE = false;
    const CONTAINERIZABLE = false;

    protected $container_blocks;
    protected $builder;
    protected $builderLoaded = false;


    public function __construct(array $props = [])
    {
        $this->builder = app()->get('pageBuilder')->setContext(ContainerContext::contextualize($this));
        parent::__construct($props);
    }

    public function loadPageBuilder()
    {
        $blocks = $this->container_blocks;

        if (!is_array($blocks)) {
            $blocks = [];
        }

        if (\array_key_exists('blocks', $blocks)) {
            $blocks = $blocks['blocks'];
        }

        $this->builder->reset()->setBlocks($blocks);
        $this->builderLoaded = true;
        return $this;
    }

    public function rewind(): \SplObjectStorage
    {
        if (!$this->builderLoaded) {
            $this->loadPageBuilder();
        }

        $this->builder->blocks()->rewind();
        return $this->builder->blocks();
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

    public function builder()
    {
        return $this->builder;
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $pageBuilder = app()
                        ->get('pageBuilder.field')
                        ->setBlocks($this->config('blocks')
                            ->filter(fn ($block) => $block::IN_LIBRARY && $block::CONTAINERIZABLE)
                            ->map(fn ($block) => $block::NAME)
                            ->all()
                            )
                        ->field('container_blocks',);

        $field = $this->createFieldsBuilder();
        $field
            ->addFields($pageBuilder);

        $this->useSettingsOn($field);

        return $field;
    }

    protected function getPlainHtml(array $parameters): string
    {
        return $parameters['render'](true);
    }

    public function toArray()
    {
        return [
            'render' => function ($return = false) {
                \ob_start();
                $this->rewind();
                while ($this->haveBlocks()) {
                    $this->getTheBlock();
                }
                $render = \ob_get_clean();
                if ($return) {
                    return $render;
                }
                echo $render;
            },
            'blocks' => array_map(fn ($block) => [
                'name' => $block->getName(),
                'data' => $block->toArray(),
                'object' => \is_admin() ? '_this_' : $block
            ], iterator_to_array($this->rewind())),
        ];
    }
}
