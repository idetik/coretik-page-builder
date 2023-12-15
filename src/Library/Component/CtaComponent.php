<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use Coretik\PageBuilder\Core\Block\Traits\Customizable;
use StoutLogic\AcfBuilder\FieldsBuilder;

class CtaComponent extends BlockComponent
{
    const NAME = 'components.cta';
    const LABEL = 'Call-to-action';

    use Customizable;

    protected $link;
    protected $gtm;
    protected $seo;
    private array $cssClasses = [];

    public function fieldsBuilder(): FieldsBuilder
    {
        if (\apply_filters('pagebuilder/block/' . $this->getName() . '/gtm_enabled', true)) {
            $this->addSettings([$this, 'gtmFields']);
        }

        $this->addSettings([$this, 'seoSettings'], 1);

        $advancedLinkArgs = \apply_filters('pagebuilder/block/' . $this->getName() . '/advanced_link_args', [
            'label' => 'Lien',
            // 'post_type' => [],
            // 'taxonomy' => [],
        ]);

        $link = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $link
            ->addField('link', 'acfe_advanced_link', $advancedLinkArgs);

        \do_action('pagebuilder/block/' . $this->getName() . '/build_fields', $link, $this);

        $this->useSettingsOn($link);

        return $link;
    }

    public function gtmFields()
    {
        $gtm = new FieldsBuilder('settings.gtm');
        $gtm
            ->addGroup('gtm', ['layout' => 'row'])
                ->addTrueFalse('push_event', ['ui' => 1])
                    ->setLabel('Ajouter un évènement GTM')
                    ->setUnrequired()
                ->addGroup('datalayer', ['layout' => 'row'])
                    ->setLabel('GTM : Paramètres (onclick)')
                    ->conditional('push_event', '==', 1)
                    ->addText('event')
                        ->setLabel('Évènement')
                        ->setRequired()
                    ->addText('category')
                        ->setLabel('Catégorie')
                        ->setRequired()
                    ->addText('action')
                        ->setLabel('Action')
                        ->setRequired()
                    ->addText('label')
                        ->setLabel('Label')
                        ->setRequired()
                    ->addText('name', ['placeholder' => '<action>_<label>'])
                        ->setLabel('Nom')
                        ->setRequired()
            ->endGroup();

        return \apply_filters('pagebuilder/block/' . static::NAME . '/gtm_fields', $gtm);
    }

    public function seoSettings()
    {
        $seo = new FieldsBuilder('settings.seo');
        $seo
            ->addGroup('seo', ['layout' => 'row'])
                ->addCheckbox('rel')
                    ->setLabel('Rel')
                    ->setInstructions('Si non renseignée, l\'alternative textuelle de l\'image sera utilisée.')
                    ->addChoice('nofollow', '[nofollow] Indiquer aux moteurs de recherche de ne pas suivre ce lien')
                    ->setUnrequired()
            ->endGroup();

        return \apply_filters('pagebuilder/block/' . static::NAME . '/seo_fields', $seo);
    }

    public function toArray()
    {
        return [
            'link' => $this->link,
            'gtm' => $this->gtm,
            'seo' => $this->seo,
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        if ($this->templateExists()) {
            return parent::getPlainHtml($parameters);
        }

        $link = $parameters['link'];

        if (empty($link)) {
            return '';
        }

        $attr = \apply_filters(
            'pagebuilder/block/components.cta/render/advanced_link_attrs',
            array_filter([
                'href' => $link['url'],
                'class' => $this->getCssClasses(),
            ]),
            $link,
            $this
        );

        $innerText = $link['title'];

        if ((bool)$link['target']) {
            $attr['target'] = '_blank';
            $innerText .= ' <span class="visuallyhidden">(nouvelle fenêtre)</span>';
        }

        return sprintf('<a %s>%s</a>', self::getStringFromAttributes($attr), $innerText);
    }

    protected static function getStringFromAttributes($attr)
    {
        return \implode(' ', \array_map(function ($k, $v) {
            return $k . '="' . ($k === 'href' ? \esc_url($v) : \esc_attr($v)) . '"';
        }, \array_keys($attr), $attr));
    }
}
