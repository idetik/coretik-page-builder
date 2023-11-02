<?php

namespace Coretik\PageBuilder\Blocks\Component;

use Coretik\PageBuilder\Blocks\BlockComponent;
use Coretik\PageBuilder\Blocks\Traits\Settings\AccessibilitySettings;
use Coretik\PageBuilder\Blocks\Traits\Settings\AnchorSettings;
use Coretik\PageBuilder\Blocks\Traits\Settings\VisibilitySettings;
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
        $this->addSettings([__CLASS__, 'seoSettings'], 1);

        // @todo : hide on mobile / desktop
        // $this->addSettings([__CLASS__, 'visibilitySettings']);

        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $field
            ->addImage('attachment_id', [
                'uploader' => 'wp',
                'acfe_thumbnail' => 0,
                'preview_size' => 'medium',
                'library' => 'all',
            ])
                ->setSelector('id')
                ->setLabel('Image');

        $this->useSettingsOn($field);

        return $field;
    }

    public static function seoSettings()
    {
        $seo = new FieldsBuilder('settings.seo');
        $seo
            ->addGroup('seo', ['layout' => 'row'])
                ->addText('alt')
                    ->setLabel('Alternative textuelle')
                    ->setInstructions('Si non renseignée, l\'alternative textuelle de l\'image sera utilisée.')
                    ->setUnrequired()
            ->endGroup();

        return \apply_filters('pagebuilder/block/components/' . static::NAME . '/seo_fields', $seo);
    }

    public function imageTag(string $image_size, array $attrs = [])
    {
        $parameters = $this->getParameters();

        $defaultAttrs = [
            'alt' => !empty($parameters['seo']['alt']) ? $parameters['seo']['alt'] : false,
            'aria-hidden' => !empty($parameters['aria-hidden']) ? $parameters['aria-hidden'] : false,
            'aria-label' => !empty($parameters['aria-label']) ? $parameters['aria-label'] : false,
        ];

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
