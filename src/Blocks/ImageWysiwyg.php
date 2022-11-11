<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class ImageWysiwyg extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.image-wysiwyg';
    const LABEL = 'Contenu: Image & Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1200, 542];

    protected $image;
    protected $image_format;
    protected $image_placement;
    protected $wysiwyg_placement;
    protected $reverse;
    protected $content;
    protected $cta;

    public function tag()
    {
        return \wp_get_attachment_image($this->image, 'themetik-block-image-wysiwyg');
    }

    public function toArray()
    {
        return [
            'wysiwyg' => $this->content,
            'image' => $this->tag(),
            'image_format' => $this->image_format,
            'image_placement' => $this->image_placement,
            'wysiwyg_placement' => $this->wysiwyg_placement,
            'reverse' => $this->reverse,
            'cta' => $this->cta,
        ];
    }
}
