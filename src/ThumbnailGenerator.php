<?php

namespace Coretik\PageBuilder;

use function Globalis\WP\Cubi\include_template_part;

class ThumbnailGenerator
{
    // const QUERY_VAR_GENERATE_THUMBS = 'themetik-regenerate-blocks-thumbs';
    protected $builder;
    protected $chrome;
    protected $outputDirectory;

    public function __construct($builder, $chrome = null)
    {
        $this->builder = $builder;
        $this->chrome = $chrome;
    }

    public function setOutputDirectory(string $directory): self
    {
        if (!\is_dir($directory)) {
            throw new \Exception($directory . ' is not a directory.');
        }

        if (!\is_writable($directory)) {
            throw new \Exception($directory . ' is not writable.');
        }

        $this->outputDirectory = rtrim('/', $directory);
        return $this;
    }

    // public function handleGenerateThumbs()
    // {
    //     if (!isset($_GET[static::QUERY_VAR_GENERATE_THUMBS])) {
    //         return;
    //     }

    //     $override = !empty($_GET['override']);

    //     if (!empty($_GET['layout'])) {
    //         try {
    //             [$block, $output] = @$this->generateThumb($_GET['layout'], $override);
    //             $results = [$block->getLabel() => $output];
    //         } catch (\Exception $e) {
    //             $results['errors'][$_GET['layout']] = $e->getMessage();
    //         }
    //     } else {
    //         $results = @$this->generateThumbs($override);
    //     }

    //     echo \json_encode($results);
    //     exit;
    // }

    public function generateThumbs(bool $override = false, bool $verbose = false)
    {
        $results = [];

        $layouts = $this->builder::availablesBlocks();
        \do_action('coretik/page-builder/generate-thumbs/start', count($layouts));
        foreach ($layouts as $layout) {
            try {
                [$block, $output] = $this->generateThumb($layout, $override);
                $results[$block->getLabel()] = $output;
                if ($verbose) {
                    app()->notices()->success(sprintf('%s : %s', $block->getLabel(), $output));
                }
            } catch (\Exception $e) {
                $results['errors'][$layout] = $e->getMessage();
                if ($verbose) {
                    app()->notices()->error(sprintf('%s : %s', $layout, $e->getMessage()));
                }
            }
            \do_action('coretik/page-builder/generate-thumbs/tick');
        }
        \do_action('coretik/page-builder/generate-thumbs/end');

        return $results;
    }

    public function generateThumb(string $layout, bool $override = false)
    {
        if (empty($this->chrome)) {
            throw new \Exception('A chrome binary is required to generate thumbs.');
        }

        static $base;

        if (empty($base)) {
            $base = include_template_part('base', ['html' => '%%CONTENT%%', 'header' => false, 'footer' => false], true);
        }

        $block = $this->builder->factory()->create(['acf_fc_layout' => $layout], 'screenshot');

        if (!$block::SCREENSHOTABLE) {
            throw new \Exception('Unscreenshotable');
        }

        $outputDirectory = $this->outputDirectory ?? get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'assets/images/admin/acf';
        $output = $outputDirectory . sprintf('/%s.png', \str_replace('.', DIRECTORY_SEPARATOR, $block->getName()));

        if (!$override && \file_exists($output)) {
            throw new \Exception('Already exists.');
        }

        $block->fakeIt();

        $html = str_replace('%%CONTENT%%', $block->render(true), $base);
        $headlessChromer = $this->chrome;
        $headlessChromer->setArgument('--disable-web-security', '');
        $headlessChromer->setOutputDirectory($outputDirectory . DIRECTORY_SEPARATOR);
        $headlessChromer->setHTML($html);
        $headlessChromer->setWindowSize($block::SCREEN_PREVIEW_SIZE[0], $block::SCREEN_PREVIEW_SIZE[1]);
        $headlessChromer->toScreenShot();

        $targetDir = \dirname($output);
        if (!\file_exists($targetDir)) {
            \mkdir($targetDir, 0755, true);
        }
        \rename($headlessChromer->getFilePath(), $output);

        return [$block, $output];
    }
}
