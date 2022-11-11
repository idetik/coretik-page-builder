<?php

namespace Coretik\PageBuilder\Cli\Command;

class GenerateThumbnailCommand
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
     * <layout>
     * : The block layout name of the block to render.
     *
     * ## EXAMPLES
     *
     *     wp page-builder thumbnails --override --verbose
     */
    public function thumbnails($args, $assoc_args)
    {
        \add_action('coretik/page-builder/generate-thumbs/start', function ($counter) {
            $this->progress = \WP_CLI\Utils\make_progress_bar('Generating Thumbs', $counter);
        });
        \add_action('coretik/page-builder/generate-thumbs/tick', [$this->progress, 'tick']);
        \add_action('coretik/page-builder/generate-thumbs/end', [$this->progress, 'finish']);
        $this->job->setConfig([
            'layout' => $args[0] ?? null,
            'override' => $assoc_args['override'] ?? false,
            'verbose' => $assoc_args['verbose'] ?? false,
        ])->handle();
    }
}
