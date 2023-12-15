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
                ->addCheckbox('breakpoint', ['layout' => 'horizontal'])
                    ->setUnrequired()
                    ->setLabel('Cacher sur les écrans')
                    ->setInstructions('Cocher les tailles d\'écrans dont vous souhaitez que l\'élément ne s\'affiche pas.')
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
