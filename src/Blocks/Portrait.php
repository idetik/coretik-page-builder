<?php

namespace Themetik\Services\PageBuilder\Blocks;

use Themetik\Services\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class Portrait extends Block
{
    use Traits\Flow;

    const NAME = 'content.portrait';
    const LABEL = 'Contenu: Portrait';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected $portrait_content;
    protected $portrait_image;

    protected function image()
    {
        return \wp_get_attachment_image($this->portrait_image, 'themetik-block-image-wysiwyg');
    }

    public function toArray()
    {
        return [
            'portrait_image' => $this->image(),
            'portrait_content' => (new Wysiwyg(['content' => $this->portrait_content]))->toArray()
        ];
    }
}
