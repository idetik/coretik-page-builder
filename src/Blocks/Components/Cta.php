<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Cta extends Component
{
    const NAME = 'components.cta';
    const LABEL = 'Call-to-action';

    protected $cta;

    public function fieldsBuilder(): FieldsBuilder
    {
        $buttonTypes = [
            'primary' => '<span class="view-frontend padding-left--2"><div class="btn btn--primary">' . __('Principal', 'themetik') . '</div></span>',
            'secondary' => '<span class="view-frontend padding-left--2"><div class="btn btn--secondary">' . __('Secondaire', 'themetik') . '</div></span>',
            // 'square' => '<span class="view-frontend padding-left--2"><div class="btn btn--square">' . __('Carré', 'themetik') . '</div></span>',
            'ghost' => '<span class="view-frontend padding-left--2"><div class="btn btn--ghost">' . __('Transparent', 'themetik') . '</div></span>',
            'link' => '<span class="view-frontend padding-left--2"><div class="btn btn--link">' . __('Lien', 'themetik') . '</div></span>',
            'danger' => '<span class="view-frontend padding-left--2"><div class="btn btn--danger">' . __('Danger', 'themetik') . '</div></span>',
        ];

        $type = $type ?? 'primary';
        $typeAsChoice = $typeAsChoice ?? false;
        $post_types = $post_types ?? app()->schema()->type('post')->keys()->all();

        $typeField = new FieldsBuilder('components.cta.type');
        if ($typeAsChoice) {
            if (\is_array($typeAsChoice)) {
                $choices = \array_intersect_key($buttonTypes, array_flip($typeAsChoice));
            } else {
                $choices = $buttonTypes;
            }
            $typeField->addRadio('type')->addChoices($choices)->setLabel(__('Style', 'themetik'));
        } else {
            $typeField->addField('type', 'acfe_hidden', ['default_value' => $type]);
        }

        $cta = new FieldsBuilder('components.cta');
        $cta
            ->addButtonGroup('target_type')
                ->setLabel(__('Type de lien', 'themetik'))
                ->setRequired()
                ->setDefaultValue('pagelink')
                ->addChoices([
                    'pagelink' => __('Lien vers une page du site', 'themetik'),
                    'file' => __('Lien vers un fichier', 'themetik'),
                    'url' => __('Lien libre', 'themetik'),
                ])
            ->addPageLink('href', ['post_type' => $post_types, 'allow_null' => 1])
                ->setLabel(__('Lien', 'themetik'))
                ->conditional('target_type', '==', 'pagelink')
            ->addFile('file_id', ['return_format' => 'id'])
                ->setLabel(__('Fichier', 'themetik'))
                ->conditional('target_type', '==', 'file')
            ->addUrl('url')
                ->setLabel(__('Lien', 'themetik'))
                ->conditional('target_type', '==', 'url')
            ->addText('label')
                ->setLabel('Texte du lien')
                ->setDefaultValue('En savoir plus')
            ->addAccordion('settings', ['label' => __('Réglages', 'themetik')])
                ->addText('anchor')
                    ->setLabel(__('Ancre', 'themetik'))
                    ->conditional('target_type', '==', 'pagelink')
                    ->setInstructions(__('Ajouter une ancre à l\'url, afin de placer un élément de la page en haut de l\'écran.'))
                    ->setConfig('prepend', '#')
                ->addFields($typeField);
    }

    public function toArray()
    {
        return [
            'cta' => $this->cta
        ];
    }
}
