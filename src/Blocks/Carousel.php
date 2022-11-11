<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Carousel extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.carousel';
    const LABEL = 'Contenu: Carousel';

    protected static $flow_default = 'base';

    // rows / gallery
    protected $type;

    // One element
    protected $title;
    protected $text;
    protected $cta;

    // Gallery or rows
    protected $medias;
    protected $slides; // [media, title, subtitle, cta]

    // embed / full
    protected $layout;

    private $uniqId;
    private $mounted = false;

    // FakeIt
    protected $propsFake = [
        'type' => 'rows',
        'layout' => 'full'
    ];

    private function uniqId()
    {
        if (!isset($this->uniqId)) {
            $this->uniqId = 'carousel-' . uniqid();
        }
        return $this->uniqId;
    }

    protected function glidise(array $slides): string
    {
        $this->mount();
        return include_template_part('templates/blocks/components/glide', ['entries' => $slides, 'id' => $this->uniqId(), 'automount' => false], true);
    }

    public function slides(): string
    {
        switch ($this->type) {
            case 'rows':
                $slides = [];
                foreach ($this->slides as $i => $row) {
                    $slides[] = include_template_part('templates/blocks/content/carousel-slide', [
                        'embed' => \wp_get_attachment_image($row['image'], 'themetik-third--wide', false, ['id' => $this->uniqId() . '-' . $i]),
                        'title' => $row['title'],
                        'text' => $row['text'],
                        'cta' => $row['cta']
                    ], true);
                }
                return $this->glidise($slides);

            case 'gallery':
                $medias = ($this->medias ?? []) ?: [];
                if (count($medias) === 1) {
                    $media = $medias[0];

                    switch (\get_post_mime_type($media)) {
                        case 'video/mp4':
                            $embed = include_template_part('templates/blocks/components/video', ['src' => \wp_get_attachment_url($media), 'type' => 'video/mp4', 'class' => 'embed'], true);
                            break;
                        case 'image/jpeg':
                        case 'image/png':
                        default:
                            $embed = include_template_part('templates/blocks/components/parallax', ['src' => \wp_get_attachment_image_url($media, 'themetik-third--wide')], true);
                            break;
                    }
                } else {
                    $entries = [];
                    foreach ($medias as $i => $media) {
                        $entries[] = \wp_get_attachment_image($media, 'themetik-third--wide', false, ['id' => $this->uniqId() . '-' . $i]);
                    }
                    $embed = $this->glidise($entries);
                }

                return include_template_part('templates/blocks/content/carousel-slide', [
                    'embed' => $embed,
                    'title' => $this->title,
                    'text' => $this->text,
                    'cta' => $this->cta
                ], true);
        }

        return '';
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

        $this->mounted = true;
    }

    public function toArray()
    {
        return [
            'layout' => $this->layout,
            'slides' => $this->slides(),
        ];
    }
}
