<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

trait Grid
{
    protected $acfe_layout_col;

    public function setGrid()
    {
        if (!empty($this->acfe_layout_col)) {
            $this->addWrapper([$this, 'columnWrap'], 100);
        }
    }

    public function useGrid()
    {
        return !empty($this->acfe_layout_col) && '12' !== $this->acfe_layout_col;
    }

    protected function columnWrap($component)
    {
        return include_template_part(
            'templates/blocks/components/grid/column',
            $this->columnToArray() + $this->wrapperParameters() + ['component' => $component],
            true
        );
    }

    protected function columnToArray()
    {
        return [
            'columns' => (int) ('auto' === $this->acfe_layout_col ? 12 : $this->acfe_layout_col)
        ];
    }
}
