<?php

namespace Coretik\PageBuilder\Cli\Command;

class PageBuilderCommand
{
    protected $progress;
    protected $job;

    public function __construct($job)
    {
        $this->job = $job;
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
     * ## EXAMPLES
     *
     *     wp page-builder generate_thumbnails --override --verbose
     */
    public function generate_thumbnails($args, $assoc_args)
    {
        \add_action('coretik/page-builder/generate-thumbs/start', function ($counter) {
            $progress = \WP_CLI\Utils\make_progress_bar('Generating Thumbs', $counter);

            \add_action('coretik/page-builder/generate-thumbs/tick', function () use ($progress) {
                $progress->tick();
            });
            \add_action('coretik/page-builder/generate-thumbs/end', function () use ($progress) {
                $progress->finish();
            });
        });
        $this->job->setConfig([
            'layout' => $args[0] ?? null,
            'override' => $assoc_args['override'] ?? false,
            'verbose' => $assoc_args['verbose'] ?? false,
        ])->handle();
    }
}
