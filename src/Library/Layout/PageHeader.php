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

    protected $components = [
        'breadcrumb' => Breadcrumb::class ,
        'thumbnail' => Thumbnail::class,
        'title' => TitlePrimary::class,
    ];

    public function flexibleLayoutArgs(): array
    {
        return [
            'max' => 1,
            'min' => 0,
        ];
    }
}
