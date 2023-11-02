<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


class RequiredModifier extends Modifier
{
    const NAME = 'required';

    /**
     * Set fields as required if this config didn't set before
     */
    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        foreach ($fields->getFields() as $field) {

            $config = $field->getConfig();
            if (!array_key_exists('required', $config)) {
                $field->setRequired();
            }
        }

        return $fields;
    }
}
