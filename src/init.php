<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Cli\Command\GenerateThumbnailCommand;
use Coretik\PageBuilder\Job\GenerateThumbnailJob;
use Coretik\PageBuilder\ThumbnailGenerator;
use Coretik\PageBuilder\Builder;
use Coretik\PageBuilder\BlockFactory;
use Globalis\WP\Cubi\add_action;

add_action('coretik/container/construct', function ($container) {
    $container['pageBuilder.config'] = [
        'fields.directory' => '',
    ];
    $container['pageBuilder'] = function($c) {
        return new Builder($c->get('pageBuilder.factory'), $c->get('pageBuilder.config'));
    };
    $container['pageBuilder.factory'] = function($c) {
        return new BlockFactory();
    };
    $container['pageBuilder.thumbnailGenerator'] = function($c) {
        return new ThumbnailGenerator($c->get('pageBuilder'), $c->get('chrome'));
    };
    $container['pageBuilder.job'] = $container->factory(function($c) {
        return new GenerateThumbnailJob($c->get('pageBuilder.thumbnailGenerator'));
    });
    $container['pageBuilder.commands'] = function($c) {
        return new GenerateThumbnailCommand($c->get('pageBuilder.job'));
    };
});
