<?php

namespace Themetik\Services\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Themetik\Services\PageBuilder\Blocks\Block;
use Themetik\Services\PageBuilder\Blocks\ImageParallax;

use function Globalis\WP\Cubi\include_template_part;

class Thumbnail extends Block
{
    const NAME = 'components.thumbnail';
    const LABEL = 'Image à la une';

    protected $thumbnail;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder(static::NAME, $this->fieldsBuilderConfig());
        $field->addImage('thumbnail', [
            'label' => __('Image à la une', 'themetik'),
            'return_format' => 'id',
            'uploader' => 'wp',
            'acfe_thumbnail' => 1,
            'preview_size' => 'themetik-50--medium',
            'library' => 'all',
        ]);
        return $field;
    }

    public function toArray()
    {
        return [
            'id' => $this->thumbnail,
            'ratio' => 25,
        ];
    }
}
