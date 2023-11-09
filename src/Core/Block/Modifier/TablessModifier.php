<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;
use StoutLogic\AcfBuilder\TabBuilder;


class TablessModifier extends Modifier
{
    const NAME = 'tabless';

    /**
     * Remove tabs
     */
    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        foreach ($fields->getFields() as $field) {

            if ($field instanceof TabBuilder) {
                $fields->removeField($field->getName());
            }
        }

        return $fields;
    }
}
