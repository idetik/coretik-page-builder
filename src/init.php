<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Cli\Command\GenerateThumbnailCommand;
use Coretik\PageBuilder\Job\GenerateThumbnailJob;
use Coretik\PageBuilder\ThumbnailGenerator;
use Coretik\PageBuilder\Builder;
use Coretik\PageBuilder\BlockFactory;
use Globalis\WP\Cubi\add_action;

add_action('coretik/container/construct', function ($container) {

    if (!class_exists('ACF')) {
        throw new \Exception('Advanced Custom fields module is required.');
    }
    if (!class_exists('ACFE')) {
        throw new \Exception('ACF Extended module is required.');
    }

    $container['pageBuilder.config'] = function ($c) {
        return collect([
            'fields.directory' => 'src/admin/fields/blocks/',
            'fields.thumbnails.directory' => \get_stylesheet_directory() . '/assets/images/admin/acf/',
            'fields.thumbnails.baseUrl' => '<##ASSETS_URL##>/images/admin/acf/',
            'blocks.template.directory' => 'templates/blocks/',
            'blocks.acf.directory' => 'templates/blocks/',
        ])->filter();
    };

    $container['pageBuilder'] = function($c) {
        return new Builder($c->get('pageBuilder.factory'), $c->get('pageBuilder.config'));
    };
    $container['pageBuilder.factory'] = function($c) {
        return new BlockFactory($c->get('pageBuilder.config'));
    };
    $container['pageBuilder.thumbnailGenerator'] = function($c) {
        $chrome = $c->has('chrome') ? $c->get('chrome') : null;
        $generator = new ThumbnailGenerator($c->get('pageBuilder'), $chrome);
        $generator->setOutputDirectory($c->get('fields.thumbnails.directory'));
        return $generator;
    };
    $container['pageBuilder.job'] = $container->factory(function($c) {
        return new GenerateThumbnailJob($c->get('pageBuilder.thumbnailGenerator'));
    });
    $container['pageBuilder.commands'] = function($c) {
        return new GenerateThumbnailCommand($c->get('pageBuilder.job'));
    };
});
