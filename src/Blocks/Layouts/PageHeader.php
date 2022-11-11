<?php

namespace Themetik\Services\PageBuilder\Blocks\Layouts;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Themetik\Services\PageBuilder\Blocks\Block;
use Themetik\Services\PageBuilder\Blocks\Breadcrumb;
use Themetik\Services\PageBuilder\Blocks\Components\Thumbnail;
use Themetik\Services\PageBuilder\Blocks\Headings\TitlePrimary;
use Themetik\Services\PageBuilder\Blocks\Traits\Composite;
use Themetik\Services\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class PageHeader extends Block
{
    use Composite;

    const NAME = 'layouts.page-header';
    const LABEL = 'En-tête de page';

    protected $components = [
        Breadcrumb::class ,
        Thumbnail::class,
        TitlePrimary::class,
    ];

    public function flexibleLayoutArgs(): array
    {
        return [
            'max' => 1,
            'min' => 0,
        ];
    }

    public function toArray()
    {
        return [
            'breadcrumb' => $this->renderComponent(Breadcrumb::class),
            'thumbnail' => $this->renderComponent(Thumbnail::class),
            'title' => $this->renderComponent(TitlePrimary::class),
        ];
    }
}
