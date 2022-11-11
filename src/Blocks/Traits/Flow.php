<?php

namespace Themetik\Services\PageBuilder\Blocks\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

trait Flow
{
    protected $flow;
    protected $flow_advanced;

    protected function initializeFlow()
    {
        $this->addWrapper([$this, 'flowWrap'], 99);
    }

    protected function flowWrap($component)
    {
        if (empty($this->flow())) {
            return $component;
        }

        return include_template_part(
            'templates/blocks/components/flow',
            $this->flowToArray() + $this->wrapperParameters() + ['component' => $component],
            true
        );
    }

    protected function flow()
    {
        switch ($this->flow ?? static::$flow_default ?? 'base') {
            case 'none':
                return '';
            case 'base':
                return static::$flow_base_default ?? 'flow';
            case 'advanced':
                $margins = [];
                foreach ($this->flow_advanced as $position => $coeff) {
                    $margins[] = sprintf('margin-%s--%s', $position, $coeff);
                }
                return \implode(' ', $margins);
            default:
                return 'flow flow--' . $this->flow;
        }
    }

    protected function flowToArray()
    {
        return [
            'flow' => $this->flow()
        ];
    }
}
