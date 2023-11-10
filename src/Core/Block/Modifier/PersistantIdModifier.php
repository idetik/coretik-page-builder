<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


class PersistantIdModifier extends Modifier
{
    const NAME = 'persistantId';
    const PRIORITY = 1;
    const SINGLETON = false;

    protected BlockInterface $block;
    protected static bool $hooked = false;

    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        $this->block = $block;
        $uniqId = $fields->addField('uniqId', 'acfe_hidden')
            ->setUnrequired();

        if (!static::$hooked) {
            \add_filter('acf/load_value/name=' . $uniqId->getName(), [$this, 'resolveId'], 10, 3);
            static::$hooked = true;
        }

        return $fields;
    }

    public function resolveId($value, $post_id, $field)
    {
        $layout = '';
        if (array_key_exists('parent_layout', $field)) {
            $layout = str_replace($field['parent'] . '_', '', $field['parent_layout']);
            $layout = str_replace('.', '-', $layout);
            $layout .= '-';
        }

        $value = !empty($value) ? $value : uniqid($layout);
        return $value;
    }
}
