<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use Coretik\PageBuilder\Library\Settings\AccessibilitySettings;
use Coretik\PageBuilder\Library\Settings\AnchorSettings;
use Coretik\PageBuilder\Library\Settings\VisibilitySettings;
use StoutLogic\AcfBuilder\FieldsBuilder;

class ImageComponent extends BlockComponent
{
    const NAME = 'components.image';
    const LABEL = 'Image';

    use AccessibilitySettings;
    use AnchorSettings;
    use VisibilitySettings;

    protected $attachment_id;
    protected $seo;

    public function fieldsBuilder(): FieldsBuilder
    {
        $this->addSettings([$this, 'seoSettings'], 1);

        $field = $this->createFieldsBuilder();
        $field
            ->addImage('attachment_id', [
                'uploader' => 'wp',
                'acfe_thumbnail' => 0,
                'preview_size' => 'medium',
                'library' => 'all',
                'return_format' => 'id'
            ])
                ->setLabel('Image');

        $this->useSettingsOn($field);

        return $field;
    }

    public function seoSettings()
    {
        $seo = new FieldsBuilder('settings.seo');
        $seo
            ->addGroup('seo', ['layout' => 'row'])
                ->addText('alt')
                    ->setLabel('Alternative textuelle')
                    ->setInstructions('Si non renseignée, l\'alternative textuelle de l\'image sera utilisée.')
                    ->setUnrequired()
            ->endGroup();

        return \apply_filters('pagebuilder/block/' . static::NAME . '/seo_fields', $seo);
    }

    public function imageTag(string $image_size, array $attrs = [])
    {
        $parameters = $this->getParameters();

        $defaultAttrs = \apply_filters('pagebuilder/block/' . static::NAME . '/image_tag_default_attrs', [
            'alt' => !empty($parameters['seo']['alt']) ? $parameters['seo']['alt'] : false,
            'aria-hidden' => !empty($parameters['accessibility']['aria_hidden']) ? $parameters['accessibility']['aria_hidden'] : false,
            'aria-label' => !empty($parameters['accessibility']['aria_label']) ? $parameters['accessibility']['aria_label'] : false,
            'class' => !empty($parameters['visibility']['breakpoint'])
                ? implode(
                    ' ',
                    array_map(
                        fn ($breakpints) => 'hide-if-' . $breakpints,
                        $parameters['visibility']['breakpoint']
                    )
                )
                : false,
        ], $parameters, $image_size);

        return \wp_get_attachment_image($parameters['attachment_id'], $image_size, false, \array_filter(\wp_parse_args($attrs, $defaultAttrs)));
    }

    protected function getPlainHtml(array $parameters): string
    {
        if (\locate_template($this->template())) {
            return parent::getPlainHtml($parameters);
        }

        return $this->imageTag('medium');
    }

    public function toArray()
    {
        return [
            'attachment_id' => $this->attachment_id,
            'seo' => $this->seo,
            'image_tag' => fn ($image_size, $attrs = []) => $this->imageTag($image_size, $attrs)
        ];
    }
}
