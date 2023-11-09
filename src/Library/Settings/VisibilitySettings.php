<?php

namespace Coretik\PageBuilder\Library\Settings;

use StoutLogic\AcfBuilder\FieldsBuilder;

trait VisibilitySettings
{
    protected $visibility;

    protected function initializeVisibilitySettings()
    {
        $this->addSettings([$this, 'visibilitySettings'], 10);
    }

    protected function visibilitySettings()
    {
        $visibility = new FieldsBuilder('settings.visibility');

        $visibility = $visibility
            ->addGroup('visibility', ['layout' => 'row'])
                ->setLabel('Affichage')
                ->addCheckbox('breakpoint', ['layout' => 'horizontal', 'default_value' => ['mobile', 'tablet', 'desktop']])
                    ->setRequired()
                    ->setLabel('Afficher sur les écrans')
                    ->setInstructions('Cocher les tailles d\'écrans dont vous souhaitez que l\'élément s\'affiche.')
                    ->addChoice('mobile', 'Mobile')
                    ->addChoice('tablet', 'Tablette')
                    ->addChoice('desktop', 'Grand écran');

        $visibility = $visibility->endGroup();

        return $visibility;
    }

    protected function visibilitySettingsToArray()
    {
        return [
            'visibility' => $this->visibility,
        ];
    }
}
