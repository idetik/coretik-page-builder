<?php

namespace Coretik\PageBuilder;

use Coretik\PageBuilder\Core\Contract\BlockFactoryInterface;
use Illuminate\Support\Collection;

if (!function_exists(__NAMESPACE__ . '\\factory')) {
    function factory(): BlockFactoryInterface
    {
        return app()->get('pageBuilder.factory');
    }
}

if (!function_exists(__NAMESPACE__ . '\\library')) {
    function library(): array
    {
        return app()->get('pageBuilder')->library();
    }
}

if (!function_exists(__NAMESPACE__ . '\\blocks')) {
    function blocks(): Collection
    {
        return app()->get('pageBuilder.config')->get('blocks');
    }
}
