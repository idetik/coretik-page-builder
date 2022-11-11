<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Breadcrumb extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'components.breadcrumb';
    const LABEL = "Fil d'ariane";

    protected $breadcrumb = null;
    protected $propsFake = [
        'breadcrumb' => [
            ['url' => '#', 'title' => 'Lorem', 'current' => false],
            ['url' => '#', 'title' => 'Ipsum', 'current' => true],
        ]
    ];

    public function toArray()
    {
        if (is_admin() && app()->has('navigation')) {
            $this->breadcrumb = app()->navigation()->partsFactory('page')->setId(\acfe_get_post_id())->setCurrent()->breadcrumb()->map(function ($row) {
                return $row->toArray();
            })->all();
        }

        return [
            'breadcrumb' => $this->breadcrumb
        ];
    }
}
