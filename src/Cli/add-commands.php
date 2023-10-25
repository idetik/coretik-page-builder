<?php

namespace Coretik\PageBuilder\Cli;

use function Globalis\WP\Cubi\add_action;

add_action('coretik/app/init', function ($app) {
    if (\defined('WP_CLI') && WP_CLI) {
        //@todo pagebuilder.commands->get('create-block')
        \WP_CLI::add_command('page-builder', $app->get('pageBuilder.commands'));
    }
});
