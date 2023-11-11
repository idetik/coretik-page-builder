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
    protected static bool $hooked = false;

    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        $displayDevToolsTab = \apply_filters('coretik/page-builder/block/devtools/display_tab', !$block instanceof BlockComponent && !$block->isChild(), $this);
        $displayDevToolsTab = \apply_filters('coretik/page-builder/block/devtools/display_tab/id=' . $block->getUniqId(), $displayDevToolsTab, $this);
        if (!$displayDevToolsTab) {
            return $fields;
        }

        $fields->addTab('DevTools');
        $devTools = $fields->addField('devtools', 'acfe_dynamic_render', ['label' => '']);

        if (!static::$hooked) {
            \add_filter('acf/render_field/name=' . $devTools->getName(), [__CLASS__, 'loadBlockAndPrintView'], 10, 3);
            static::$hooked = true;
        }

        return $fields;
    }

    public static function loadBlockAndPrintView($field)
    {
        // Get layout name from current acf field
        $layout_name = str_replace($field['parent'] . '_', '', $field['parent_layout']);

        // Get all filled layouts from current pagebuilder
        $current_layouts = get_field($field['parent']);

        // Pick layouts same as our $layout_name
        $layouts_filtered = array_filter($current_layouts, fn ($l) => $l['acf_fc_layout'] === $layout_name);

        // Get the current layout values
        $layout = $layouts_filtered[static::$counter];
        static::$counter++;

        // Create new block instance
        $block = app()->get('pageBuilder.factory')->create($layout);
        if (empty($layout['uniqId'])) {
            $layout['uniqId'] = $block->getUniqId();
        }
        $block->setProps($layout);

        echo $block->getDevToolsView();
    }
}
