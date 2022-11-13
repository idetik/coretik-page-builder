<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Blocks\Block;

class BlockFactory
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        Block::setConfigAsGlobal($config);
    }

    public function create($layout, $context = null): BlockInterface
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

        switch ($name) {
            default:
                $parts = \explode('.', $name);
                $parts = \array_map(fn ($part) => \str_replace(['_', '-'], '', \ucwords($part, '-')), $parts);
                $block = __NAMESPACE__ . '\\Blocks\\';
                $block .= implode('\\', $parts);
                if (\class_exists($block)) {
                    $block = new $block($layout);
                } else {
                    throw new \Exception('Undefined layout ' . $layout['acf_fc_layout']);
                }
                break;
        }

        if (!empty($context)) {
            $block->setContext($context);
        }

        return $block;
    }
}
