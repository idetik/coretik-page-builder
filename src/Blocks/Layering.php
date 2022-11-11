<?php

namespace Themetik\Services\PageBuilder\Blocks;

use Themetik\Services\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class Layering extends Block
{
    use Traits\Flow;

    const NAME = 'content.layering';
    const LABEL = 'Contenu: Blocs superposés';
    const SCREEN_PREVIEW_SIZE = [1200, 542];

    protected $backward_content;
    protected $backward_image;
    protected $backward_cta;
    protected $forward_content;
    protected $forward_cta;

    protected function imageUrl()
    {
        return \wp_get_attachment_image_url($this->backward_image, 'large');
    }

    protected function resolveCta(array $group): array
    {
        $use_cta = !empty($group['layering_use_cta']) && $group['layering_use_cta'];
        if (!$use_cta) {
            return [];
        }
        return $group['layering_cta'];
    }

    public function toArray()
    {
        return [
            'backward_content' => (new Wysiwyg(['content' => $this->backward_content]))->toArray(),
            'forward_content' => (new Wysiwyg(['content' => $this->forward_content]))->toArray(),
            'backward_url_image' => $this->imageUrl(),
            'backward_cta' => $this->resolveCta($this->backward_cta),
            'forward_cta' => $this->resolveCta($this->forward_cta),
        ];
    }
}
