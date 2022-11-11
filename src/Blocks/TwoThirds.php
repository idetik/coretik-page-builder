<?php

namespace Themetik\Services\PageBuilder\Blocks;

class TwoThirds extends Block
{
    use Traits\Flow;

    const NAME = 'headings.two-thirds';
    const LABEL = 'En tête : Titre';

    protected $surtitle;
    protected $title;
    protected $subtitle;
    protected $image;
    protected $image_position;
    protected $image_transition;
    protected $image_mobile;
    protected $cta;
    protected $settings;

    public function imageTag()
    {
        $classes = ['embed', 'embed--cover'];
        switch ($this->image_position) {
            case 'left':
                $classes[] = 'embed--left';
                break;
            case 'right':
                $classes[] = 'embed--right';
                break;
            case 'center':
            default:
                $classes[] = 'embed--center';
                break;
        }
        return $this->image ? \wp_get_attachment_image($this->image, 'themetik-block-two-thirds', false, ['class' => implode(' ', $classes)]) : '';
    }

    public function imageMobileTag()
    {
        return $this->image_mobile ? \wp_get_attachment_image($this->image_mobile, 'themetik-50--medium', false, ['class' => 'embed']) : '';
    }

    // @todo title tag => first block heading?
    public function toArray()
    {
        return [
            'surtitle' => $this->surtitle,
            'title' => $this->title,
            'text' => $this->subtitle,
            'image' => $this->imageTag(),
            'image_mobile' => $this->imageMobileTag(),
            'image_transition' => $this->image_transition,
            'cta' => $this->cta,
            'settings' => $this->settings
        ];
    }
}
