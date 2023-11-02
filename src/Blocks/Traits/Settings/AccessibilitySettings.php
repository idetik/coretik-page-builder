<?php

namespace Coretik\PageBuilder\Blocks\Traits\Settings;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

trait AccessibilitySettings
{
    // protected $aria_hidden;
    // protected $aria_label;
    protected $accessibility;

    protected function initializeAccessibilitySettings()
    {
        $this->addSettings([$this, 'accessibilitySettings'], 10);
    }

    protected function accessibilitySettings()
    {
        $accessibility = new FieldsBuilder('settings.accessibility');

        $accessibility = $accessibility
            ->addGroup('accessibility', ['layout' => 'row'])
                ->setLabel('Accessibilité');

        if ($this instanceof BlockComponent) {
            $accessibility
                ->addTrueFalse('aria_hidden', ['message' => 'Masquer cet élément pour les liseuses d\'écran'])
                    ->setUnrequired()
                    ->setLabel('Aria hidden');
        }

        $accessibility
            ->addText('aria_label')
                ->setUnrequired()
                ->setLabel('Aria label')
                ->setInstructions('Nom accessible qui ne sera visible que par les technologies d\'assistance.')
                ->conditional('aria_hidden', '!=', 1);

        $accessibility = $accessibility->endGroup();

        return $accessibility;
    }

    protected function accessibilitySettingsToArray()
    {
        return [
            'accessibility' => $this->accessibility,
        ];
    }
}
