<?php

namespace Coretik\PageBuilder\Blocks\Traits\Settings;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

trait AnchorSettings
{
    protected $anchor;

    protected function initializeAnchorSettings()
    {
        $this->addSettings([$this, 'anchorSettings'], 10);
    }

    protected function anchorSettings()
    {
        $anchor = new FieldsBuilder('settings.anchor');
        $anchor
            ->addGroup('anchor', ['layout' => 'row'])
                ->setLabel('Ancre')
                ->addText('attr_id')
                    ->setInstructions('Placer un identifiant unique. Celui-ci pourra être ciblé par une ancre.')
                    ->setUnrequired()
                    ->setLabel('Id')
            ->end();

        return $anchor;
    }

    protected function anchorSettingsToArray()
    {
        return [
            'anchor' => $this->anchor
        ];
    }
}
