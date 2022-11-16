<?php

namespace Coretik\PageBuilder\Acf;

use StoutLogic\AcfBuilder\FieldsBuilder;

class PageBuilderField
{
    protected $service;
    protected $base;

    public function __construct($service)
    {
        $this->service = $service;
    }

    protected function base(string $field_name = 'base-blocks')
    {
        if (empty($this->base)) {
            $blocks = $this->service->availablesBlocks();
            $builder = new FieldsBuilder('');
            $builder->addFlexibleContent($field_name);
            foreach ($blocks as $layout) {
                if ('containers.container' === $layout) {
                    continue;
                }
                try {
                    $block = $this->service->factory()->create(['acf_fc_layout' => $layout]);
                    $builder->getField($field_name)->addLayout($block->fields(), $block->flexibleLayoutArgs());
                } catch (\Exception $e) {
                    app()->notices()->error($e->getMessage());
                }
            }
            $this->base = $builder;
        }
        return $this->base;
    }

    public function field(string $field_name, array $restricted_layouts = [], array $acfe_config = [], bool $with_containers = true)
    {
        $builder = new FieldsBuilder('page_builder', [
            'position' => 'normal',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label'
        ]);

        $acfe_config_default = [
            'label' => __('Constructeur de page', app()->get('settings')['text-domain']),
            'acfe_flexible_advanced' => 1,
            'acfe_flexible_stylised_button' => 1,
            'acfe_flexible_hide_empty_message' => 0,
            'acfe_flexible_empty_message' => '',
            'acfe_flexible_disable_ajax_title' => 1,
            'acfe_flexible_layouts_thumbnails' => 1,
            'acfe_flexible_layouts_settings' => 1,
            'acfe_flexible_layouts_ajax' => 1,
            'acfe_flexible_layouts_templates' => 1,
            'acfe_flexible_layouts_previews' => 1,
            'acfe_flexible_layouts_placeholder' => 1,
            'acfe_flexible_title_edition' => 1,
            'acfe_flexible_clone' => 0,
            'acfe_flexible_copy_paste' => 1,
            'acfe_flexible_toggle' => 1,
            'acfe_flexible_close_button' => 1,
            'acfe_flexible_remove_add_button' => 0,
            'acfe_flexible_remove_duplicate_button' => 0,
            'acfe_flexible_remove_delete_button' => 0,
            'acfe_flexible_lock' => 0,
            'acfe_flexible_modal_edit' => [
                'acfe_flexible_modal_edit_enabled' => 1,
                'acfe_flexible_modal_edit_size' => 'full',
            ],
            'acfe_flexible_modal' => [
                'acfe_flexible_modal_enabled' => '1',
                'acfe_flexible_modal_title' => __('Ajouter un élément', app()->get('settings')['text-domain']),
                'acfe_flexible_modal_col' => '3',
                'acfe_flexible_modal_categories' => '1',
            ],
            'acfe_flexible_layouts_state' => '',
            'acfe_flexible_layouts_remove_collapse' => 0,
            'button_label' => __('Ajouter un élément', app()->get('settings')['text-domain']),
            'acfe_flexible_grid' => [
                'acfe_flexible_grid_enabled' => '1',
                'acfe_flexible_grid_align' => 'center',
                'acfe_flexible_grid_valign' => 'stretch',
                'acfe_flexible_grid_wrap' => '0',
            ],
            'acfe_flexible_grid_container' => '',
        ];

        $base_layouts = $this->base()->getField('base-blocks')->getLayouts();

        if (!empty($restricted_layouts)) {
            $layouts = array_filter($base_layouts, function ($layout) use ($restricted_layouts) {
                return \in_array($layout->getName(), $restricted_layouts);
            });
        } else {
            $layouts = $base_layouts;
        }

        $builder
            ->addFlexibleContent($field_name, wp_parse_args($acfe_config ?? [], $acfe_config_default))
                ->addLayouts($layouts);

        if ($with_containers) {
            try {
                $block = $this->service->factory()->create(['acf_fc_layout' => 'containers.container'], $context ?? null)->fields();
                $builder->getField($field_name)->addLayout($block);
            } catch (\Exception $e) {
                app()->notices()->error($e->getMessage());
            }
        }

        return $builder;
    }
}
