<?php

namespace Themetik\Services\PageBuilder\Blocks;

class PageTitle extends Block
{
    use Traits\Flow;

    const NAME = 'headings.page-title';
    const LABEL = 'En tête : Titre';

    protected $title;
    protected $image;
    protected $parallax;
    protected $overlay;
    protected $overlay_settings;
    protected $propsFake = [
        'title' => 'Lorem Ipsum dolor it',
    ];

    protected static $flow_default = 'base';

    public function imageSrc()
    {
        return $this->image ? \wp_get_attachment_image_url($this->image, 'themetik-third--wide') : '';
    }

    public function toArray()
    {
        if (empty($this->title) && !empty(\acfe_get_post_id())) {
            $model_id = \acf_decode_post_id(\acfe_get_post_id())['id'];
            $model = app()->schema()->get('page', 'post')->model((int)$model_id);
            $this->title = $model->title();
        }

        return [
            'title' => $this->title,
            'img_src' => $this->imageSrc(),
            'img_id' => $this->image,
            'parallax' => $this->parallax,
            'overlay' => $this->overlay ? $this->overlay_settings : false,
        ];
    }
}
