<?php

namespace Coretik\PageBuilder\Blocks\Components;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Cta extends BlockComponent
{
    const NAME = 'components.cta';
    const LABEL = 'Call-to-action';

    protected $cta;

    public function fieldsBuilder(null|array|bool $typeAsChoice = null, ?array $postTypes = null): FieldsBuilder
    {
        $ctaTypes = \apply_filters('coretik/page-builder/blocks/components/cta/types', [
            'primary' => '<div class="btn btn--primary" aria-label="Primary">Call to action</div>',
            'secondary' => '<div class="btn btn--secondary" aria-label="Secondary">Call to action</div>',
        ]);

        $typeAsChoice = $typeAsChoice ?? false;
        $postTypes = $postTypes ?? app()->schema()->type('post')->keys()->all();

        $typeField = new FieldsBuilder('components.cta.type');
        if ($typeAsChoice) {
            if (\is_array($typeAsChoice)) {
                $choices = \array_intersect_key($ctaTypes, array_flip($typeAsChoice));
            } else {
                $choices = $ctaTypes;
            }
            $typeField->addRadio('type')->addChoices($choices)->setLabel('Style');
        } else {
            $typeField->addField('type', 'acfe_hidden', ['default_value' => key($ctaTypes)]);
        }

        $cta = new FieldsBuilder('components.cta');
        $cta
            ->addButtonGroup('target_type')
                ->setLabel(__('Type de lien', app()->get('settings')['text-domain']))
                ->setRequired()
                ->setDefaultValue('pagelink')
                ->addChoices([
                    'pagelink' => __('Lien vers une page du site', app()->get('settings')['text-domain']),
                    'file' => __('Lien vers un fichier', app()->get('settings')['text-domain']),
                    'url' => __('Lien libre', app()->get('settings')['text-domain']),
                ])
            ->addPageLink('href', ['post_type' => $postTypes, 'allow_null' => 1])
                ->setLabel(__('Lien', app()->get('settings')['text-domain']))
                ->conditional('target_type', '==', 'pagelink')
            ->addFile('file_id', ['return_format' => 'id'])
                ->setLabel(__('Fichier', app()->get('settings')['text-domain']))
                ->conditional('target_type', '==', 'file')
            ->addUrl('url')
                ->setLabel(__('Lien', app()->get('settings')['text-domain']))
                ->conditional('target_type', '==', 'url')
            ->addText('label')
                ->setLabel(__('Texte du lien', app()->get('settings')['text-domain']))
                ->setDefaultValue('En savoir plus')
            ->addAccordion('settings', ['label' => __('Paramètres du lien', app()->get('settings')['text-domain'])])
                ->addText('anchor')
                    ->setLabel(__('Ancre', app()->get('settings')['text-domain']))
                    ->conditional('target_type', '==', 'pagelink')
                    ->setInstructions(__('Ajouter une ancre à l\'url, afin de placer un élément de la page en haut de l\'écran.', app()->get('settings')['text-domain']))
                    ->setConfig('prepend', '#')
                ->addFields($typeField);
        return $cta;
    }

    public function toArray()
    {
        return [
            'cta' => $this->cta
        ];
    }
}
