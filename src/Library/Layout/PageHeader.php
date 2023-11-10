<?php

namespace Coretik\PageBuilder\Library\Layout;

use Coretik\PageBuilder\Core\Block\BlockComposite;
use Coretik\PageBuilder\Library\{
    Tools\Breadcrumb,
    Components\Thumbnail,
    Headings\TitlePrimary
};

class PageHeader extends BlockComposite
{
    const NAME = 'layouts.page-header';
    const LABEL = 'En-tête de page';
    const CONTAINERIZABLE = false;
    const FLEXIBLE_LAYOUT_ARGS = [
        'max' => 1,
        'min' => 0,
    ];

    protected $components = [
        'breadcrumb' => Breadcrumb::class ,
        'thumbnail' => Thumbnail::class,
        'title' => TitlePrimary::class,
    ];
}
