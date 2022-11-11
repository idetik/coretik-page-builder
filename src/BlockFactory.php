<?php

namespace Coretik\PageBuilder;

class BlockFactory
{
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

        $custom = \apply_filters('themetik/services/page-builder/factory/create', null, $name, $layout, $context);
        $custom = \apply_filters('themetik/services/page-builder/factory/create/name=' . $name, $custom, $name, $layout, $context);
        if (!empty($custom) && $custom instanceof BlockInterface) {
            if (!empty($context)) {
                $custom->setContext($context);
            }
            return $custom;
        }

        switch ($name) {
            case 'content.image-parallax':
                $block = new Blocks\ImageParallax($layout);
                break;
            case 'content.embed-blocks':
                $block = new Blocks\EmbedBlocks($layout);
                break;
            case 'content.image-wysiwyg':
                $block = new Blocks\ImageWysiwyg($layout);
                break;
            case 'content.icon-wysiwyg':
                $block = new Blocks\IconWysiwyg($layout);
                break;
            case 'content.wysiwyg':
                $block = new Blocks\Wysiwyg($layout);
                break;
            case 'content.wysiwyg-double':
                $block = new Blocks\WysiwygDouble($layout);
                break;
            case 'content.services':
                $block = new Blocks\Services($layout);
                break;
            case 'content.carousel':
                $block = new Blocks\Carousel($layout);
                break;
            case 'content.last-posts-overview':
                $block = new Blocks\LastPostsOverview($layout);
                break;
            case 'content.inserts':
                $block = new Blocks\Inserts($layout);
                break;
            case 'content.logos':
                $block = new Blocks\Logos($layout);
                break;
            case 'content.contact':
                $block = new Blocks\Contact($layout);
                break;
            case 'content.gallery':
                $block = new Blocks\Gallery($layout);
                break;
            case 'content.card':
                $block = new Blocks\Card($layout);
                break;
            case 'content.layering':
                $block = new Blocks\Layering($layout);
                break;
            case 'content.portrait':
                $block = new Blocks\Portrait($layout);
                break;
            case 'content.cta':
                $block = new Blocks\Cta($layout);
                break;
            case 'content.features-pricing':
                $block = new Blocks\FeaturesPricing($layout);
                break;
            case 'headings.page-title':
                $block = new Blocks\PageTitle($layout);
                break;
            case 'headings.two-thirds':
                $block = new Blocks\TwoThirds($layout);
                break;
            case 'headings.heading-1':
                $block = new Blocks\Heading1($layout);
                break;
            case 'headings.title':
                $block = new Blocks\Title($layout);
                break;
            case 'tools.anchor':
                $block = new Blocks\Anchor($layout);
                break;
            case 'tools.breadcrumb':
                $block = new Blocks\Breadcrumb($layout);
                break;
            case 'containers.container':
                $block = new Blocks\Container($layout);
                break;
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
