<?php

namespace Themetik\Services\PageBuilder\Blocks;

class Card extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.card';
    const LABEL = 'Contenu: Carte centrale (image + texte)';
    const SCREEN_PREVIEW_SIZE = [800, 341];

    protected $content;
    protected $image;
    protected $background_inner;
    protected $border_customizer;
    protected $border_width;
    protected $border_color;

    public function toArray()
    {
        return [
            'image' => $this->image,
            'content' => $this->content,
            'border' => $this->border_customizer ? [
                'color' => $this->border_color,
                'width' => $this->border_width . 'px',
            ] : false,
            'background_inner' => $this->background_inner,
        ];
    }
}
