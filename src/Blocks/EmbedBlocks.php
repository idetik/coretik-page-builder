<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class EmbedBlocks extends Block
{
    use Traits\Flow;

    const NAME = 'content.embed-blocks';
    const LABEL = 'Contenu: Présentation double';
    const SCREEN_PREVIEW_SIZE = [1400, 633];

    protected static $flow_default = 'base';

    protected $title;
    protected $subtitle_use_media_legend;
    protected $subtitle;
    protected $subtext;
    protected $medias;
    protected $block_1;
    protected $block_2;

    private $uniqId;
    private $mounted = false;

    private function uniqId()
    {
        if (!isset($this->uniqId)) {
            $this->uniqId = 'embed-blocks-' . uniqid();
        }
        return $this->uniqId;
    }

    public function embed()
    {
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
            // Glide
            $entries = [];
            foreach ($medias as $i => $media) {
                $entries[] = \wp_get_attachment_image($media, 'themetik-third--wide', false, ['id' => $this->uniqId() . '-' . $i]);
            }
            $embed = include_template_part('templates/blocks/components/glide', ['entries' => $entries, 'id' => $this->uniqId(), 'automount' => false, 'useBullets' => false], true);
            $this->mount();
        }

        return $embed;
    }

    protected function legends()
    {
        $medias = ($this->medias ?? []) ?: [];

        if (empty($medias)) {
            return '';
        }

        if (count($medias) === 1) {
            return \wp_get_attachment_caption(current($medias));
        } else {
            $legends = [];
            foreach ($medias as $i => $media) {
                $hidden = $i !== 0;
                $legends[] = sprintf(
                    '<span id="%s" %s>%s</span>',
                    $this->uniqId() . '-caption-' . $i,
                    $hidden ? 'style="display:none"' : '',
                    \wp_get_attachment_caption($media)
                );
            }
            return \implode('', $legends);
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
                    <?php if ($this->subtitle_use_media_legend) : ?>
                        var idPrefix = '<?= $this->uniqId() ?>-caption-';
                        var currentIndex = glide.index;
                        $('#' + idPrefix + currentIndex).show();
                        glide.on('run', function (el) {
                            $('#' + idPrefix + currentIndex).fadeOut(150, function() {
                                currentIndex = glide.index;
                                $('#' + idPrefix + currentIndex).fadeIn(100);
                            });
                        });
                    <?php endif; ?>
                });
            </script>
            <?php
        }, 99);
    }

    public function toArray()
    {
        return [
            'embed' => $this->embed(),
            'title' => $this->title,
            'titleTag' => 'h1',
            'text' => $this->subtitle_use_media_legend ? $this->legends() : $this->subtitle,
            'subtext' => $this->subtext,
            'blocks' => [
                $this->block_1,
                $this->block_2,
            ],
        ];
    }
}
