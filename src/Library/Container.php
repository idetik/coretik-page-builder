<?php

namespace Coretik\PageBuilder\Library;

use Coretik\PageBuilder\Core\Block\ParentContext;
use Coretik\PageBuilder\Core\Block\Block;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Container extends Block
{
    const NAME = 'containers.container';
    const LABEL = 'Conteneur';
    const SCREENSHOTABLE = false;

    protected $container_blocks;
    protected $padding;
    protected $builder;
    protected $builderLoaded = false;
    protected $border_customizer;
    protected $border_width;
    protected $border_color;


    public function __construct(array $props = [])
    {
        $this->builder = app()->get('pageBuilder')->setContext(ParentContext::contextualize($this));
        $this->setProps($props);
        $this->initialize();

        if (\is_admin()) {
            \add_action('acfe/flexible/render/before_template/layout=' . $this->fields()->getName(), function () {
                $data = get_fields();
                $data = current(current($data));
                $this->setProps($data)->loadPageBuilder()->render();
            });
        }
    }

    protected function loadPageBuilder()
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

    public function rewind()
    {
        if (!$this->builderLoaded) {
            $this->loadPageBuilder();
        }

        $this->builder->blocks()->rewind();
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
                        ->field(
                            'container_blocks',
                            $this->config('blocks')
                                ->filter(fn ($block) => $block::IN_LIBRARY && $block::CONTAINERIZABLE)
                                ->map(fn ($block) => $block::NAME)
                                ->all(),
                            [],
                            false
                        );

        $field = $this->createFieldsBuilder();
        $field
            ->addFields($pageBuilder);

        $this->useSettingsOn($field);

        return $field;
    }

    public function toArray()
    {
        return [
            'blocks' => $this,
        ];
    }
}
