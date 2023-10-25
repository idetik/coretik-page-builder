<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Cli\Command\PageBuilderCommand;
use Coretik\PageBuilder\Job\GenerateThumbnailJob;
use Coretik\PageBuilder\Acf\PageBuilderField;
use Coretik\PageBuilder\{Builder, BlockFactory, ThumbnailGenerator};
use Coretik\PageBuilder\Blocks\Components\{Thumbnail, Cta, Wysiwyg as WysiwygComponent};
use Coretik\PageBuilder\Blocks\Tools\{Anchor, Breadcrumb};
use Coretik\PageBuilder\Blocks\Content\{Wysiwyg, WysiwygDouble};
use Coretik\PageBuilder\Blocks\Layouts\{PageHeader};
use Coretik\PageBuilder\Blocks\Headings\{TitlePrimary};
use Coretik\PageBuilder\Blocks\Container;

use function Globalis\WP\Cubi\add_action;

add_action('coretik/container/construct', function ($container) {

    if (!\class_exists('ACF')) {
        $container->notices()->error('Advanced Custom fields module is required.');
        return;
    }

    if (!\class_exists('ACFE')) {
        $container->notices()->error('ACF Extended module is required.');
        return;
    }

    // Extend it: $container->extend('pageBuilder.blocks', fn ($blocks, $c) => $blocks->append(...));
    $container['pageBuilder.blocks'] = function ($c) {
        return \collect([
            // Components
            Cta::class,
            WysiwygComponent::class,
            Thumbnail::class,

            // Tools
            Anchor::class,
            Breadcrumb::class,

            // Layouts
            PageHeader::class,

            // Headings
            TitlePrimary::class,

            // Content
            Wysiwyg::class,
            WysiwygDouble::class,

            // Container
            Container::class,
        ]);
    };

    $container['pageBuilder.config'] = function ($c) {
        return \collect([
            'fields.directory' => 'src/admin/fields/blocks/',
            'fields.thumbnails.directory' => \get_stylesheet_directory() . '/assets/images/admin/acf/',
            'fields.thumbnails.baseUrl' => '<##ASSETS_URL##>/images/admin/acf/',
            'blocks.template.directory' => 'templates/blocks/',
            'blocks.acf.directory' => 'templates/acf/',
            'blocks' => $c->get('pageBuilder.blocks')
        ])->filter();
    };

    $container['pageBuilder.factory'] = function ($c) {
        return new BlockFactory($c->get('pageBuilder.config'));
    };

    $container['pageBuilder'] = $container->factory(function ($c) {
        return new Builder($c->get('pageBuilder.factory'), $c->get('pageBuilder.config'));
    });

    $container['pageBuilder.thumbnailGenerator'] = function ($c) {
        $chrome = $c->has('chrome') ? $c->get('chrome') : null;
        $generator = new ThumbnailGenerator($c->get('pageBuilder'), $chrome);
        $generator->setOutputDirectory($c->get('pageBuilder.config')->get('fields.thumbnails.directory'));
        return $generator;
    };

    $container['pageBuilder.job'] = $container->factory(function ($c) {
        return new GenerateThumbnailJob($c->get('pageBuilder.thumbnailGenerator'));
    });

    $container['pageBuilder.commands'] = function ($c) {
        return new PageBuilderCommand($c->get('pageBuilder.job'), $c->get('pageBuilder.config'));
    };

    $container['pageBuilder.field'] = function ($c) {
        return new PageBuilderField($c->get('pageBuilder'));
    };
});
