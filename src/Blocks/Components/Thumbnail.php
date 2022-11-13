<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;
use Coretik\PageBuilder\Blocks\ImageParallax;

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

    public function toArray()
    {
        return [
            'id' => $this->thumbnail
        ];
    }
}
