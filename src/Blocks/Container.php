<?php

namespace Coretik\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Traits\Grid;

use function Globalis\WP\Cubi\include_template_part;

class Container extends Block
{
    use Grid;
    use Traits\Flow;
    use Traits\Background;
    use Traits\Container {
        containerToArray as containerToArrayTrait;
    }

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
        $this->builder = app()->get('pageBuilder')->setContext(static::NAME);
        $this->setProps($props);
        $this->initialize();

        if (\is_admin()) {
            if (!in_array($this->getName(), static::$fieldsHooked)) {
                $block = $this;
                \add_action('acfe/flexible/render/before_template/layout=' . $this->fields()->getName(), function () use ($block) {
                    $data = get_fields();
                    $data = current(current($data));
                    $block->setProps($data)->loadPageBuilder()->render();
                });
                static::$fieldsHooked[] = $this->getName();
            }
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

    public function toArray()
    {
        return [
            'blocks' => $this,
            'padding' => $this->padding,
            'border' => $this->border_customizer ? [
                'color' => $this->border_color,
                'width' => $this->border_width . 'px',
            ] : false,
        ];
    }

    public function containerToArray()
    {
        $data = $this->containerToArrayTrait();
        $data['padding'] = false;
        return $data;
    }
}
