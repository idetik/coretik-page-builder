<?php

namespace Coretik\PageBuilder\Library\Component;

use StoutLogic\AcfBuilder\FieldsBuilder;

class ThumbnailComponent extends ImageComponent
{
    const NAME = 'components.thumbnail';
    const LABEL = 'Image à la une';
    const FLEXIBLE_LAYOUT_ARGS = [
        'max' => 1,
        'min' => 0,
    ];

    protected $thumbnail;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
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
}
