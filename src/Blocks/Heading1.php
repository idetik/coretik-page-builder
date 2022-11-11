<?php

namespace Themetik\Services\PageBuilder\Blocks;

use function Globalis\WP\Cubi\include_template_part;

class Heading1 extends Block
{
    use Traits\Flow;

    const NAME = 'headings.heading-1';
    const LABEL = 'En tête : Titre 1';

    protected $medias;
    protected $surtitle;
    protected $title;
    protected $subtitle;
    protected $image;
    protected $image_mobile;
    protected $cta;
    protected $ratio;
    protected $blur;
    protected $overlay;
    protected $overlay_settings;

    private $uniqId;
    private $mounted = false;

    public function __construct(array $props = [])
    {
        parent::__construct($props);
        if (defined('WP_ENV') && 'development' === WP_ENV) {
            $this->propsFake['medias'] = [app()->faker()->attachmentId()];
        }
    }

    private function uniqId()
    {
        if (!isset($this->uniqId)) {
            $this->uniqId = 'heading1-' . uniqid();
        }
        return $this->uniqId;
    }

    public function imageSize()
    {
        switch ($this->ratio) {
            case 'screen':
                $image_size = 'themetik-125--wide';
            case 'auto':
            default:
                $image_size = 'themetik-third--wide';
                break;
        }

        return $image_size;
    }

    public function embed()
    {
        $medias = ($this->medias ?? []) ?: [];
        if (count($medias) === 1) {
            $media = $medias[0];
            switch (\get_post_mime_type($media)) {
                case 'video/mp4':
                    $embed = include_template_part('templates/blocks/components/video', ['src' => \wp_get_attachment_url($media), 'type' => 'video/mp4', 'class' => 'heading-video__embed'], true);
                    break;
                case 'image/jpeg':
                case 'image/png':
                default:
                    $embed = \wp_get_attachment_image($media, $this->imageSize(), false, ['class' => 'heading-video__embed']);
                    // $embed = include_template_part('templates/blocks/components/parallax', ['src' => \wp_get_attachment_image_url($media, $this->imageSize())], true);
                    break;
            }
        } else {
            // Glide
            $entries = [];
            foreach ($medias as $i => $media) {
                $entries[] = \wp_get_attachment_image($media, $this->imageSize(), false, ['id' => $this->uniqId() . '-' . $i]);
            }
            $embed = sprintf(
                '<div class="heading-video__embed heading-video__embed--glide">%s</div>',
                include_template_part('templates/blocks/components/glide', ['entries' => $entries, 'id' => $this->uniqId(), 'automount' => false, 'useBullets' => false], true)
            );
            $this->mount();
        }

        return $embed;
    }

    protected function mount()
    {
        if ($this->mounted) {
            return;
        }

        \add_action('wp_footer', function () {
            ?>
            <script type="text/javascript">
                $(window).on("load", function () {
                    var glide = new window.Glide('#<?= $this->uniqId() ?>', {
                        autoplay: 4000,
                        gap: 0
                    }).mount();
                });
            </script>
            <?php
        }, 99);
    }

    public function settings()
    {
        return [
            'background-image-url' => \wp_get_attachment_image_url($this->image_mobile, $this->imageSize()),
        ];
    }

    // @todo title tag => first block heading?
    public function toArray()
    {
        return [
            'id' => $this->uniqId(),
            'surtitle' => $this->surtitle,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'embed' => $this->embed(),
            'ratio' => $this->ratio,
            // 'image_mobile' => $this->imageMobileTag(),
            'cta' => $this->cta,
            'settings' => $this->settings(),
            'overlay' => $this->overlay ? $this->overlay_settings : false,
            'blur' => $this->blur,
        ];
    }
}
