<?php

namespace Coretik\PageBuilder\Core\Acf;

use StoutLogic\AcfBuilder\FieldsBuilder;

class PageBuilderField
{
    protected $service;
    protected $base;
    protected array $config = [];
    protected array $blocks = [];

    public function __construct($service)
    {
        $this->service = $service;
        $this->blocks = $this->service->library();
        $this->config = [
            'label' => __('Constructeur de page', \app()->get('settings')['text-domain']),
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
            'acfe_flexible_clone' => 1,
            'acfe_flexible_copy_paste' => 1,
            'acfe_flexible_toggle' => 1,
            'acfe_flexible_close_button' => 1,
            'acfe_flexible_remove_add_button' => 0,
            'acfe_flexible_remove_duplicate_button' => 0,
            'acfe_flexible_remove_delete_button' => 0,
            'acfe_flexible_lock' => 0,
            'acfe_flexible_add_actions' => [
                // 'title', // inline edit
                'copy',
                'lock',
            ],
            'acfe_flexible_modal_edit' => [
                'acfe_flexible_modal_edit_enabled' => 0,
                'acfe_flexible_modal_edit_size' => 'large',
            ],
            'acfe_flexible_modal' => [
                'acfe_flexible_modal_enabled' => '1',
                'acfe_flexible_modal_title' => __('Ajouter un élément', \app()->get('settings')['text-domain']),
                'acfe_flexible_modal_col' => '4',
                'acfe_flexible_modal_categories' => '1',
            ],
            'acfe_flexible_layouts_state' => '',
            'acfe_flexible_layouts_remove_collapse' => 0,
            'button_label' => __('Ajouter un élément', \app()->get('settings')['text-domain']),
            'acfe_flexible_grid' => [
                'acfe_flexible_grid_enabled' => '0',
                'acfe_flexible_grid_align' => 'center',
                'acfe_flexible_grid_valign' => 'stretch',
                'acfe_flexible_grid_wrap' => '0',
            ],
            'acfe_flexible_grid_container' => '',
        ];
    }

    public function setConfig(array $config): self
    {
        $this->config = \wp_parse_args($config, $this->config);
        return $this;
    }

    public function setBlocks(array $blocks): self
    {
        $this->blocks = $blocks;
        return $this;
    }

    public function field(string $field_name)
    {
        $builder = new FieldsBuilder('page_builder', [
            'position' => 'normal',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label'
        ]);
        $args = \apply_filters('coretik/page-builder/acf/page-builder-field/args', $this->config, $field_name);
        $flexible = $builder->addFlexibleContent($field_name, $args);

        foreach ($this->blocks as $layout) {
            if ('containers.container' === $layout) {
                continue;
            }
            try {
                $block = $this->service->factory()->create(['acf_fc_layout' => $layout]);
                $flexible->addLayout($block->fields(), $block->flexibleLayoutArgs());
            } catch (\Exception $e) {
                \app()->notices()->error($e->getMessage());
            }
        }

        return $builder;
    }
}
