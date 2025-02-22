<?php

namespace Coretik\PageBuilder;

/**
 * Coretik Pagebuilder v2
 * ----------------------
 */

use Coretik\Core\Utils\Arr;
use Coretik\PageBuilder\Core\Acf\PageBuilderField;
use Coretik\PageBuilder\Core\Block\BlockFactory;
use Coretik\PageBuilder\Library\Component\{
    AnchorComponent,
    BreadcrumbComponent,
    CtaComponent,
    ImageComponent,
    RepeatearComponent,
    ThumbnailComponent,
    WysiwygComponent,
    TableComponent,
    TitleComponent,
    TextComponent,
};
use Coretik\PageBuilder\Library\Container;
use Coretik\PageBuilder\Builder;
use Coretik\PageBuilder\Cli\Command\PageBuilderCommand;
use Coretik\PageBuilder\Core\Contract\BlockFactoryInterface;
use Coretik\PageBuilder\Core\Contract\ShouldBuildBlockType;
use Coretik\PageBuilder\Core\Job\Block\CreateBlockJob;
use Coretik\PageBuilder\Core\Job\Thumbnail\{
    GenerateThumbnailJob,
    ThumbnailGenerator
};
use Illuminate\Support\Collection;

use function Globalis\WP\Cubi\add_action;
use function Globalis\WP\Cubi\is_cli;

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

    /**
     * Extend it:
     * $container->extend('pageBuilder.library', fn ($blocks, $c) => $blocks->append(...));
     *
     * Or filter it:
     * add_filter('coretik/page-builder/library', function ($library) {
     *     return $library;
     * })
     */
    $container['pageBuilder.library'] = function ($c) {
        return \collect(
            \apply_filters('coretik/page-builder/init_library', [
                    // Components
                    AnchorComponent::class,
                    BreadcrumbComponent::class,
                    CtaComponent::class,
                    ImageComponent::class,
                    TableComponent::class,
                    TextComponent::class,
                    ThumbnailComponent::class,
                    TitleComponent::class,
                    WysiwygComponent::class,
                    RepeatearComponent::class,


                    // Blocks


                    // Layouts
                    // SampleLayout::class,


                    // // Container
                    Container::class,
                ])
        );
    };

    $container['pageBuilder.config'] = function ($c): Collection {
        return \collect(
            \apply_filters('coretik/page-builder/config', [
                'fields.directory' => 'src/admin/fields/blocks/',
                'fields.thumbnails.directory' => \get_stylesheet_directory() . '/assets/images/admin/acf/',
                'fields.thumbnails.baseUrl' => '<##ASSETS_URL##>/images/admin/acf/',
                'blocks.template.directory' => 'templates/blocks/',
                'blocks.acf.directory' => 'templates/acf/',
                'blocks.src.directory' => 'src/Services/PageBuilder/Blocks/',
                'blocks.rootNamespace' => ($c['rootNamespace'] ?? 'App') . '\\Services\\PageBuilder\\Blocks',
                'blocks' => $c->get('pageBuilder.library')
            ])
        )->filter();
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

    $container['pageBuilder.field'] = $container->factory(function ($c) {
        return new PageBuilderField($c->get('pageBuilder'));
    });
});

/**
 * Admin hooks
 */
add_action('admin_init', function () {

    /**
     * Blocks preview
     */
    add_action('acfe/flexible/render/before_template', function ($field, $layout) {
        $layoutName = $layout['name'];

        if (\in_array($layoutName, app()->get('pageBuilder.config')->get('blocks')->map(fn ($block) => $block::NAME)->all())) {
            $data = get_fields();
            $data = current(current($data));
            $block = app()->get('pageBuilder.factory')->create($data);

            if (empty($data['uniqId'])) {
                $data['uniqId'] = $block->getUniqId();
            }
            $block->setProps($data);
            \do_action('coretik/page-builder/block/load', $block, $data);
            \do_action('coretik/page-builder/block/load/layoutId=' . $block->getLayoutId(), $block, $data);
            \do_action('coretik/page-builder/block/load/uniqId=' . $block->getUniqId(), $block, $data);
            $block->render();
        }
    }, 10, 2);
});


add_action('acf/init', function() {
    foreach (blocks() as $block) {
        $block = factory()->create(['acf_fc_layout' => $block]);

        $fields = $block->fields();
        $fields->setLocation('block', '==', 'acf/acf-' . \str_replace('.', '-', $block::NAME));
        
        acf_add_local_field_group($fields->build());
    }
});

if (!function_exists(__NAMESPACE__ . '\\factory')) {
    function factory(): BlockFactoryInterface
    {
        return app()->get('pageBuilder.factory');
    }
}

if (!function_exists(__NAMESPACE__ . '\\blocks')) {
    function blocks(): Collection
    {
        return app()->get('pageBuilder')->library();
    }
}

add_action('init', function () {
    foreach (blocks() as $blockName) {

        $block = factory()->find($blockName);
        if (in_array(ShouldBuildBlockType::class, class_implements($block))) {
            $block = factory()->create($blockName)->registerBlockType();
            // $block = factory()->create(['acf_fc_layout' => $block])->registerBlockType();
        }


        //@todo json($block) => format block_type
        //@todo filter library to get only blocks with JSONABLE

        // acf_register_block_type([
        //     'name' => 'acf/' . \str_replace('.', '-', $block::NAME),
        //     'title' => $block::LABEL,
        //     'category' => $block::CATEGORY ?? 'common',
        //     // 'icon' => $block::ICON ?? 'block-default-icon',
        //     // 'keywords' => $block::KEYWORDS ?? [],
        //     'render_callback' => function ($attributes, $content) use ($block) {
        //         // $attributes['acf_fc_layout'] = $block::NAME;

        //         $data = array_merge($attributes['data'], [
        //             'acf_fc_layout' => $block::NAME,
        //         ]);

        //         $data = get_fields();
        //         $data['acf_fc_layout'] = $block::NAME;

        //         $block = app()->get('pageBuilder.factory')->create($data);
        //         if (empty($data['uniqId'])) {
        //             $data['uniqId'] = $block->getUniqId();
        //         }
        //         $block->setProps($data);
        //         \do_action('coretik/page-builder/block/load', $block, $attributes);
        //         \do_action('coretik/page-builder/block/load/layoutId=' . $block->getLayoutId(), $block, $attributes);
        //         \do_action('coretik/page-builder/block/load/uniqId=' . $block->getUniqId(), $block, $attributes);
        //         $block->render();
        //     }
        // ]);
    }
});