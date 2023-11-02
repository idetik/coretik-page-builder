<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Blocks\Component\TitleComponent;
use Coretik\PageBuilder\Blocks\Layout\ParagraphLayout;
use Coretik\PageBuilder\Cli\Command\PageBuilderCommand;
use Coretik\PageBuilder\Job\Thumbnail\{
    GenerateThumbnailJob,
    ThumbnailGenerator
};
use Coretik\PageBuilder\Job\Block\CreateBlockJob;
use Coretik\PageBuilder\Acf\PageBuilderField;
use Coretik\PageBuilder\{Builder, BlockFactory};
use Coretik\PageBuilder\Blocks\Component\{
    ThumbnailComponent,
    WysiwygComponent,
    LinkComponent
};
use Coretik\PageBuilder\Blocks\Tools\{Anchor, Breadcrumb};
use Coretik\PageBuilder\Blocks\Block\Editorial\{WysiwygBlock, WysiwygDoubleBlock};
use Coretik\PageBuilder\Blocks\Layouts\{PageHeader};
use Coretik\PageBuilder\Blocks\Headings\{TitlePrimary};
use Coretik\PageBuilder\Blocks\Container;

use function Globalis\WP\Cubi\add_action;
use function Globalis\WP\Cubi\is_cli;

/**
 * @todo supprimer
 */
require_once __DIR__ ."/Blocks/Modifier/modifiers.php";

add_action('coretik/container/construct', function ($container) {

    if (!\class_exists('ACF')) {
        if (\is_admin() && !is_cli()) {
            $container->get('notices')->error('Advanced Custom fields module is required.');
        }
    }

    if (!\class_exists('ACFE')) {
        if (\is_admin() && !is_cli()) {
            $container->get('notices')->error('ACF Extended module is required.');
        }
    }

    // Extend it: $container->extend('pageBuilder.blocks', fn ($blocks, $c) => $blocks->append(...));
    $container['pageBuilder.blocks'] = function ($c) {
        return \collect([
            // Components
            // CtaComponent::class,
            WysiwygComponent::class,
            ThumbnailComponent::class,
            TitleComponent::class,
            LinkComponent::class,

            // // Tools
            // Anchor::class,
            // Breadcrumb::class,

            // // Layouts
            // PageHeader::class,
            ParagraphLayout::class,

            // // Headings
            // TitlePrimary::class,

            // // Content
            WysiwygBlock::class,
            WysiwygDoubleBlock::class,

            // // Container
            // Container::class,
        ]);
    };

    $container['pageBuilder.config'] = function ($c) {
        $globalSettings = $c->get('settings');

        return \collect([
            'fields.directory' => 'src/admin/fields/blocks/',
            'fields.thumbnails.directory' => \get_stylesheet_directory() . '/assets/images/admin/acf/',
            'fields.thumbnails.baseUrl' => '<##ASSETS_URL##>/images/admin/acf/',
            'blocks.template.directory' => 'templates/blocks/',
            'blocks.acf.directory' => 'templates/acf/',
            'blocks.src.directory' => 'src/Services/PageBuilder/Blocks/',
            'blocks.rootNamespace' => $globalSettings['rootNamespace'] ?? 'App' . '\\Services\\PageBuilder\\Blocks',
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

    $container['pageBuilder.job.generate-thumbnail'] = $container->factory(function ($c) {
        return new GenerateThumbnailJob($c->get('pageBuilder.thumbnailGenerator'));
    });

    $container['pageBuilder.job.create-block'] = $container->factory(function ($c) {
        return new CreateBlockJob($c);
    });

    $container['pageBuilder.commands'] = function ($c) {
        return PageBuilderCommand::make($c->get('pageBuilder.config'))
            ->setThumbnailJob($c->get('pageBuilder.job.generate-thumbnail'))
            ->setBlockJob($c->get('pageBuilder.job.create-block'));
    };

    $container['pageBuilder.field'] = function ($c) {
        return new PageBuilderField($c->get('pageBuilder'));
    };
});
