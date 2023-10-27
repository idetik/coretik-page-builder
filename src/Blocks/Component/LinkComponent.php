<?php

namespace Coretik\PageBuilder\Blocks\Component;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class LinkComponent extends BlockComponent
{
    const NAME = 'components.link';
    const LABEL = 'Call-to-action';

    protected $link;

    public function fieldsBuilder(): FieldsBuilder
    {
        $this->addSettings([__CLASS__, 'gtmFields']);

        $advancedLinkArgs = \apply_filters('pagebuilder/block/components/' . $this->getName() . '/advanced_link_args', [
            'label' => 'Lien',
            // 'post_type' => [],
            // 'taxonomy' => [],
        ]);

        $link = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $link
            ->addField('link', 'acfe_advanced_link', $advancedLinkArgs);

        \do_action('pagebuilder/block/components/' . $this->getName() . '/build_fields', $link, $this);

        $this->useSettingsOn($link);

        return $link;
    }

    public static function gtmFields()
    {
        $gtm = new FieldsBuilder('setting.gtm');
        $gtm
            ->addTrueFalse('use_gtm', ['ui' => 1])
                ->setLabel('Ajouter un évènement GTM');
        
        return $gtm;
    }

    public function toArray()
    {
        return [
            'link' => $this->link,
            'use_gtm' => $this->use_gtm,
        ];
    }

    protected function getPlainHtml(): string
    {
        if (\locate_template($this->template())) {
            return parent::getPlainHtml();
        }

        $link = $this->link;

        $attr = \apply_filters('pagebuilder/block/components/' . $this->getName() . '/render/advanced_link_attrs', [
                'href' => $link['url'],
                'class' => $className,
            ],
            $link,
            $this
        );
    
        $innerText = $link['title'];
    
        if ((bool)$link['target']) {
            $attr['target'] = '_blank';
            $innerText.= ' <span class="visuallyhidden">(nouvelle fenêtre)</span>';
        }

        return sprintf('<a %s>%s<a>', self::getStringFromAttributes($attr), $innerText);
    }

    protected static function getStringFromAttributes($attr)
    {
        return \implode(' ', \array_map(function ($k, $v) {
            return $k . '="' . ($k === 'href' ? \esc_url($v) : \esc_attr($v)) . '"';
        }, \array_keys($attr), $attr));
    }
    
}
