<?php

namespace Coretik\PageBuilder\Cli\Command;

class PageBuilderCommand
{
    protected $progress;
    protected $job;
    protected $config;

    public function __construct($job, $config)
    {
        $this->job = $job;
        $this->config = $config;
    }

    /**
     * @todo create-component
     * @todo create-block
     * @todo create-layout
     * @todo create-modifier
     * @todo create-settings
     */

    /**
     * Create block
     *
     * ## OPTIONS
     *
     * [<layout_name>]
     * : The block layout name
     *
     * [--verbose]
     * : Echo logs
     *
     * [--format=<format>]
     * : Output json results
     *
     * ## EXAMPLES
     *
     *     wp page-builder create_block layout.my_layout
     */
    public function create_block($args, $assoc_args)
    {
        $verbose = $assoc_args['verbose'] ?? false;
        $format = $assoc_args['format'] ?? false;

        if ($verbose) {
            \add_action('coretik/page-builder/generate-thumbs/start', function ($counter) {
                $progress = \WP_CLI\Utils\make_progress_bar('Generating Thumbs', $counter);

                \add_action('coretik/page-builder/generate-thumbs/tick', function () use ($progress) {
                    $progress->tick();
                });
                \add_action('coretik/page-builder/generate-thumbs/end', function () use ($progress) {
                    $progress->finish();
                });
            });
        }

        $results = $this->job->setConfig([
            'layout' => $args[0] ?? null,
            'override' => $assoc_args['override'] ?? false,
            'verbose' => $verbose,
        ])->handle();

        if ('json' === $format) {
            $formatted = [];

            if (!empty($results['errors'])) {
                foreach ($results['errors'] as $layout => $message) {
                    $formatted[$layout] = [
                        'success' => false,
                        'message' => $message
                    ];
                }
                unset($results['errors']);
            }

            foreach (($results) as $layout => $message) {
                $formatted[$layout] = [
                    'success' => true,
                    'message' => $message
                ];
            }

            echo \json_encode($formatted);
        }
    }

    /**
     * Create block's preview images
     *
     * ## OPTIONS
     *
     * [<layout>]
     * : The block layout name of the block to render.
     *
     * [--verbose]
     * : Echo logs
     *
     * [--override]
     * : Override existing block thumbnail
     *
     * [--format=<format>]
     * : Output json results
     *
     * ## EXAMPLES
     *
     *     wp page-builder generate_thumbnails --override --verbose
     */
    public function generate_thumbnails($args, $assoc_args)
    {
        $verbose = $assoc_args['verbose'] ?? false;
        $format = $assoc_args['format'] ?? false;

        if ($verbose) {
            \add_action('coretik/page-builder/generate-thumbs/start', function ($counter) {
                $progress = \WP_CLI\Utils\make_progress_bar('Generating Thumbs', $counter);

                \add_action('coretik/page-builder/generate-thumbs/tick', function () use ($progress) {
                    $progress->tick();
                });
                \add_action('coretik/page-builder/generate-thumbs/end', function () use ($progress) {
                    $progress->finish();
                });
            });
        }

        $results = $this->job->setConfig([
            'layout' => $args[0] ?? null,
            'override' => $assoc_args['override'] ?? false,
            'verbose' => $verbose,
        ])->handle();

        if ('json' === $format) {
            $formatted = [];

            if (!empty($results['errors'])) {
                foreach ($results['errors'] as $layout => $message) {
                    $formatted[$layout] = [
                        'success' => false,
                        'message' => $message
                    ];
                }
                unset($results['errors']);
            }

            foreach (($results) as $layout => $message) {
                $formatted[$layout] = [
                    'success' => true,
                    'message' => $message
                ];
            }

            echo \json_encode($formatted);
        }
    }

    /**
     * Return all blocks attached to page builder
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Output format results
     *
     * ## EXAMPLES
     *
     *     wp page-builder get_blocks --format=json
     */
    public function get_blocks($args, $assoc_args)
    {
        $blocks = $this->config->get('blocks')
                    ->map(fn ($block) => ['category' => $block::category(), 'name' => $block::NAME])
                    ->all();

        $format = \WP_CLI\Utils\get_flag_value($assoc_args, 'format', 'json');
        \WP_CLI\Utils\format_items(
            $format,
            $blocks,
            ['category', 'name']
        );
    }
}
