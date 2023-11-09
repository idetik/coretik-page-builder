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

    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        $displayDevToolsTab = \apply_filters('coretik/page-builder/block/devtools/display_tab', !$block instanceof BlockComponent, $this);
        $displayDevToolsTab = \apply_filters('coretik/page-builder/block/devtools/display_tab/id=' . $block->getUniqId(), $displayDevToolsTab, $this);
        if (!$displayDevToolsTab) {
            return $fields;
        }

        if ($this->handled) {
            return $fields;
        }

        $fields->addTab('DevTools');
        $message = $fields->addField('devtools_' . $block->getUniqId(), 'acfe_dynamic_render', ['label' => '']);
        \add_action('acf/render_field/name=' . $message->getName(), fn () => print($block->getDevToolsView()));
        $this->handled = true;
        return $fields;
    }
}
