<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

class ImageParallax extends Block
{
    use Traits\Flow;

    const NAME = 'content.image-parallax';
    const LABEL = 'Contenu: Image pleine largeur';

    protected $ratio;
    protected $image;
    protected $alt;

    protected static $flow_default = 'none';

    public function imageSize()
    {
        switch ($this->ratio) {
            case '33':
                $image_size = 'themetik-two-thirds--wide';
            case '25':
            default:
                $image_size = 'themetik-half--wide';
                break;
        }

        return $image_size;
    }

    public function imageUrl()
    {
        return \wp_get_attachment_image_url($this->image, $this->imageSize());
    }

    public function alt()
    {
        return ($this->alt ?? false) ?: \wp_get_attachment_caption($this->image);
    }

    public function toArray()
    {
        return [
            'url' => $this->imageUrl(),
            'alt' => $this->alt(),
            'ratio' => $this->ratio,
        ];
    }
}
