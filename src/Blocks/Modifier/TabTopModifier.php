<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;
use StoutLogic\AcfBuilder\TabBuilder;


class TabTopModifier extends Modifier
{
    const NAME = 'tabtop';

    /**
     * Change tab placement to top
     */
    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        foreach ($fields->getFields() as $field) {

            if ($field instanceof TabBuilder) {
                $field->setConfig('placement', 'top');
            }

        }

        return $fields;
    }
}
