<?php

namespace Themetik\Services\PageBuilder\Blocks;

use Themetik\Services\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class ThreeColumns implements BlockInterface
{
    const TEMPLATE = "templates/page-builder/blocks/three-columns";

    protected $column_1;
    protected $column_2;
    protected $column_3;

    public function __construct(OneColumn $column_1, OneColumn $column_2, OneColumn $column_3)
    {
        $this->column_1 = $column_1;
        $this->column_2 = $column_2;
        $this->column_3 = $column_3;
    }

    public function toArray()
    {
        $array = [];
        foreach ([$this->column_1, $this->column_2, $this->column_3] as $column) {
            $array[] = $column->render();
        }
        return $array;
    }

    public function render()
    {
        return include_template_part(static::TEMPLATE, ['columns' => $this->toArray()], true);
    }
}
