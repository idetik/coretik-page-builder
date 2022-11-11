<?php

namespace Themetik\Services\PageBuilder\Blocks\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

trait Container
{
    protected $container;
    protected $padding;

    protected function initializeContainer()
    {
        if (!\array_key_exists('container', $this->propsFake)) {
            $this->propsFake = \array_merge($this->propsFake, [
                'container' => 'full',
                'padding' => 0
            ]);
        }

        $this->addWrapper([$this, 'containerWrap'], 50);
    }

    protected function containerWrap($component)
    {
        switch ($this->container) {
            case 'full':
            default:
                return $component;
            case 'contain':
            case 'contain-editorial':
                return include_template_part(
                    'templates/blocks/components/container',
                    $this->containerToArray() + $this->wrapperParameters() + ['component' => $component],
                    true
                );
        }
    }

    protected function containerToArray()
    {
        return [
            'contained' => $this->container === 'contain',
            'editorial' => $this->container === 'contain-editorial',
            'padding' => $this->padding,
        ];
    }
}
