<?php

namespace Coretik\PageBuilder\Core\Block;

use Coretik\PageBuilder\Core\Contract\{
    BlockContextInterface,
    BlockFactoryInterface,
    BlockInterface,
};
use ArrayAccess;
use Exception;

class BlockFactory implements BlockFactoryInterface
{
    protected $config;

    public function __construct(ArrayAccess $config)
    {
        $config['factory'] = $this;
        $this->config = $config;
        Block::setConfigAsGlobal($config);
    }

    public function create(string|array $layout, BlockContextInterface $context = null): BlockInterface
    {
        if (\is_array($layout)) {
            $name = $layout['acf_fc_layout'];
        } else {
            $name = $layout;
            $layout = [
                'acf_fc_layout' => $name
            ];
        }

        $custom = \apply_filters('coretik/page-builder/factory/create', null, $name, $layout, $context, $this->config);
        $custom = \apply_filters('coretik/page-builder/factory/create/name=' . $name, $custom, $name, $layout, $context, $this->config);
        if (!empty($custom) && $custom instanceof BlockInterface) {
            if (!empty($context)) {
                $custom->setContext($context);
            }
            return $custom;
        }

        $block = $this->config['blocks']->first(fn ($block) => $block::NAME === $name);
        if ($block) {
            $block = new $block($layout);
        } else {
            throw new Exception('Undefined layout ' . $layout['acf_fc_layout']);
        }

        if (!empty($context)) {
            $block->setContext($context);
        }

        return $block;
    }
}
