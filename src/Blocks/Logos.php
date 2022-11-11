<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Logos extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.logos';
    const LABEL = 'Contenu: Carousel de logos';

    protected static $flow_default = 'base';

    protected $logos;
    private $uniqId;
    private $mounted = false;

    private function uniqId()
    {
        if (!isset($this->uniqId)) {
            $this->uniqId = 'logos-' . uniqid();
        }
        return $this->uniqId;
    }

    protected function glidise(array $slides): string
    {
        $this->mount();
        return include_template_part('templates/blocks/components/glide', [
            'entries' => $slides,
            'id' => $this->uniqId(),
            'automount' => false,
            'useBullets' => false
        ], true);
    }

    public function slides(): string
    {
        $medias = ($this->logos ?? []) ?: [];
        $entries = [];
        foreach ($medias as $i => $media) {
            $tag = \wp_get_attachment_image($media, 'small', false, ['class' => 'embed embed--contain embed--center']);
            $entries[] = sprintf('<div class="embed-container embed-container--ratio-100" id="%s" style="max-width:15rem;min-height:12rem;">%s</div>', $this->uniqId() . '-' . $i, $tag);
        }
        $embed = $this->glidise($entries);
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
                        type: 'carousel',
                        autoplay: 4000,
                        focusAt: 'center',
                        perView: 5,
                        gap: 100,
                        peek: 200,
                        breakpoints: {
                            768: {
                                gap: 40,
                                peek: 80,
                                perView: 2,
                            },
                            1024: {
                                gap: 100,
                                peek: 200,
                                perView: 3,
                            }
                        }
                    }).mount();
                });
            </script>
            <?php
        }, 99);
    }

    public function toArray()
    {
        return [
            'slides' => $this->slides(),
        ];
    }
}
