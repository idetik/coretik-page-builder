<?php

namespace Coretik\PageBuilder\Cli\Command;

use Coretik\PageBuilder\Job\JobInterface;
use Illuminate\Support\Collection;
use Coretik\PageBuilder\Job\Block\BlockType;

class PageBuilderCommand
{
    protected $progress;
    protected JobInterface $thumbnailJob;
    protected JobInterface $blockJob;
    protected $config;

    public function __construct(Collection $config)
    {
        $this->config = $config;
    }

    public static function make(Collection $config): self
    {
        return new static($config);
    }

    public function setThumbnailJob(JobInterface $job): self
    {
        $this->thumbnailJob = $job;
        return $this;
    }

    public function setBlockJob(JobInterface $job): self
    {
        $this->blockJob = $job;
        return $this;
    }

    /**
     * Create block
     *
     * ## OPTIONS
     *
     * [<class>]
     * : The block classname
     * 
     * [--type=<block_type>]
     * : The block type
     * ---
     * default: block
     * options:
     *   - block
     *   - component
     *   - composite
     * ---
     *
     * [--name=<name>]
     * : The block name to retrieve template (ex: components.title, template based in blocks/components/title.php)
     *
     * [--label=<label>]
     * : The block title
     * 
     * [--verbose]
     * : Echo logs
     *
     * [--force]
     * : Override existings files
     *
     * ## EXAMPLES
     *
     *     wp page-builder create Components/MyComponent --name=components.my-component --type=component --label="My super Component" --verbose --force
     */
    public function create($args, $assoc_args)
    {
        $class = \rtrim($args[0], '.php');
        $verbose = $assoc_args['verbose'] ?? false;
        $name = $assoc_args['name'] ?? null;
        $label = $assoc_args['label'] ?? null;
        $type = $assoc_args['type'] ?? false;
        $force = $assoc_args['force'] ?? false;

        $this->blockJob->setConfig([
            'class' => $class,
            'force' => $force,
            'verbose' => $verbose,
            'name' => $name,
            'label' => $label,
        ])->setBlockType(match ($type) {
            'component' => BlockType::Component,
            'block' => BlockType::Block,
            'composite' => BlockType::Composite,
            default => BlockType::Block,
        })->handle();
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

        $this->thumbnailJob->setConfig([
            'layout' => $args[0] ?? null,
            'override' => $assoc_args['override'] ?? false,
            'verbose' => $verbose,
        ])->handle();

        $results = $this->thumbnailJob->getPayload();

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
                    ->map(fn ($block) => ['category' => $block::categoryTitle(), 'name' => $block::NAME])
                    ->all();

        $format = \WP_CLI\Utils\get_flag_value($assoc_args, 'format', 'json');
        \WP_CLI\Utils\format_items(
            $format,
            $blocks,
            ['category', 'name']
        );
    }
}
