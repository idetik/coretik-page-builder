<?php

namespace Coretik\PageBuilder\Blocks\Layouts;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;
use Coretik\PageBuilder\Blocks\Tools\Breadcrumb;
use Coretik\PageBuilder\Blocks\Components\Thumbnail;
use Coretik\PageBuilder\Blocks\Headings\TitlePrimary;
use Coretik\PageBuilder\Blocks\Traits\Composite;

use function Globalis\WP\Cubi\include_template_part;

class PageHeader extends Block
{
    use Composite;

    const NAME = 'layouts.page-header';
    const LABEL = 'En-tête de page';
    const CONTAINERIZABLE = false;

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

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());

        foreach ($this->fieldsComposite() as $name => $data) {
            $field->addTab($data['block']->getLabel(), ['placement' => 'left'])
                ->addFields($data['fields']);
        }

        return $field;
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
