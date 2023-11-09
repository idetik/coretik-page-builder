<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class ThumbnailComponent extends BlockComponent
{
    const NAME = 'components.thumbnail';
    const LABEL = 'Image à la une';

    protected $thumbnail;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder(static::NAME, $this->fieldsBuilderConfig());
        $field->addImage('thumbnail', [
            'label' => __('Image à la une', app()->get('settings')['text-domain']),
            'return_format' => 'id',
            'uploader' => 'wp',
            'acfe_thumbnail' => 1,
            'preview_size' => 'themetik-50--medium',
            'library' => 'all',
        ]);
        return $field;
    }

    public function flexibleLayoutArgs(): array
    {
        return [
            'max' => 1,
            'min' => 0,
        ];
    }

    /**
     * @todo renderIfNoTemplate
     */
    // public function render()
    // {
    //     wp_get_attachment_image();
    // }

    public function toArray()
    {
        return [
            'id' => $this->thumbnail
        ];
    }
}
