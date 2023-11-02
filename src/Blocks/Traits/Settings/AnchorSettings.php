<?php

namespace Coretik\PageBuilder\Blocks\Traits\Settings;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

trait AnchorSettings
{
    protected $attr_id;

    protected function initializeAnchorSettings()
    {
        $this->addSettings([$this, 'anchorSettings'], 10);
    }

    protected function anchorSettings()
    {
        $anchor = new FieldsBuilder('settings.anchor');

        if ($this instanceof BlockComponent) {
            $anchor
                ->addText('attr_id')
                    ->setInstructions('Placer un identifiant unique. Celui-ci pourra être ciblé par une ancre.')
                    ->setUnrequired()
                    ->setLabel('Id');
        }
        return $anchor;
    }

    protected function anchorSettingsToArray()
    {
        return [
            'id' => $this->attr_id
        ];
    }
}
