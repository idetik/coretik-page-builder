<?php

namespace Coretik\PageBuilder\Blocks\Traits\Settings;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

trait FlowSettings
{
    protected $flow;
    protected $flow_advanced;

    protected function initializeFlowSettings()
    {
        $this->addWrapper([$this, 'flowWrap'], 99);
        $this->addSettings([$this, 'flowSettings'], 10);
    }

    protected function flowWrap($component)
    {
        if (empty($this->flow())) {
            return $component;
        }

        return include_template_part(
            $this->config('blocks.template.directory') . '/wrapper/flow',
            $this->flowToArray() + $this->wrapperParameters() + ['component' => $component],
            true
        );
    }

    protected function flowSettings()
    {
        $flow_default = $this->flow_default ?? 'base';
        $flow_advanced = $this->flow_advanced ?? false;

        $flow_choices = \wp_parse_args(($this->flow_choices ?? []), [
            ['none' => __('Pas de marge', app()->get('settings')['text-domain'])],
            ['base' => __('Utiliser les marges par défaut', app()->get('settings')['text-domain'])],
            ['10' => __('Petites', app()->get('settings')['text-domain'])],
            ['20' => __('Grandes', app()->get('settings')['text-domain'])],
        ]);

        if ($flow_advanced) {
            $flow_choices['advanced'] = __('Personnalisées', app()->get('settings')['text-domain']);
        }

        $flow = new FieldsBuilder('trait.flow');
        $flow
            ->addButtonGroup('flow')
                ->setLabel(__('Marges', app()->get('settings')['text-domain']))
                ->setInstructions(__('Espacement avec les autres blocs'))
                ->setDefaultValue($flow_default)
                ->addChoices($flow_choices)
                ->setRequired()
            ->addGroup('flow_advanced', ['label' => '', 'layout' => 'row'])
                ->conditional('flow', '==', 'advanced')
                ->addRange('top', ['label' => __('Marge en haut', app()->get('settings')['text-domain']), 'max' => 12])
                    ->setRequired()
                    ->setDefaultValue('4')
                ->addRange('bottom', ['label' => __('Marge en bas', app()->get('settings')['text-domain']), 'max' => 12])
                    ->setRequired()
                    ->setDefaultValue('4')
                ->addRange('left', ['label' => __('Marge à gauche', app()->get('settings')['text-domain']), 'max' => 12])
                    ->setRequired()
                    ->setDefaultValue('0')
                ->addRange('right', ['label' => __('Marge à droite', app()->get('settings')['text-domain']), 'max' => 12])
                    ->setRequired()
                    ->setDefaultValue('0');
        return $flow;
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
