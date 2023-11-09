<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


class PersistantIdModifier extends Modifier
{
    const NAME = 'persistantId';
    const PRIORITY = 1;
    const SINGLETON = false;

    protected $handled = false;

    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        if ($this->handled) {
            return $fields;
        }

        $fields->addField('uniqId', 'acfe_hidden')
            ->setDefaultValue($block->getUniqId())
            ->setUnrequired();
        $this->handled = true;
        return $fields;
    }
}
