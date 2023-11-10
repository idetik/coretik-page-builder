<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;


class DevToolsTabModifier extends Modifier
{
    const NAME = 'DevToolsTab';
    const PRIORITY = 5;
    const SINGLETON = false;

    protected $handled = false;

    protected static int $counter = 0;

    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        $displayDevToolsTab = \apply_filters('coretik/page-builder/block/devtools/display_tab', !$block instanceof BlockComponent && !$block->isChild(), $this);
        $displayDevToolsTab = \apply_filters('coretik/page-builder/block/devtools/display_tab/id=' . $block->getLayoutId(), $displayDevToolsTab, $this);
        if (!$displayDevToolsTab) {
            return $fields;
        }

        if ($this->handled) {
            return $fields;
        }

        $fields->addTab('DevTools');
        $message = $fields->addField('devtools_' . $block->getLayoutId(), 'acfe_dynamic_render', ['label' => '']);

        \add_action('acf/render_field/name=' . $message->getName(), function ($field) use ($block) {

            $layout_name = str_replace($field['parent'] . '_', '', $field['parent_layout']);
            $current_layouts = get_field($field['parent']);
            $layouts_filtered = array_filter($current_layouts, fn ($l) => $l['acf_fc_layout'] === $layout_name);
            $layout = $layouts_filtered[static::$counter];
            static::$counter++;
            $block = app()->get('pageBuilder.factory')->create($layout);
            if (empty($layout['uniqId'])) {
                $layout['uniqId'] = $block->getUniqId();
            }
            $block->setProps($layout);
            print($block->getDevToolsView());
        });

        $this->handled = true;
        return $fields;
    }
}
