<?php

namespace Coretik\PageBuilder\Cli\Command;

use Coretik\PageBuilder\Cli\Command\SubCommand\{
    CreateBlockSubCommand,
    CreateComponentSubCommand,
    CreateCompositeSubCommand,
    CreateSubCommand,
};
use Coretik\PageBuilder\Core\Contract\JobInterface;
use Illuminate\Support\Collection;

class PageBuilderCommand
{
    use CreateSubCommand;
    use CreateComponentSubCommand;
    use CreateBlockSubCommand;
    use CreateCompositeSubCommand;

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

    protected static function strToPascalCase(string $src): string
    {
        return ucfirst(str_replace(' ', '', ucwords(strtr($src, '_-', ' '))));
    }

    protected static function strToSnakeCase(string $src): string
    {
        return strtolower(str_replace([' ', '-'], '_', \remove_accents($src)));
    }

    protected static function strToKebabCase(string $src): string
    {
        return strtolower(str_replace([' ', '-'], '-', \remove_accents($src)));
    }
}
