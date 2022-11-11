<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Gallery extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.gallery';
    const LABEL = 'Contenu: Gallery';

    protected static $flow_default = 'base';

    protected $gallery;
    protected $gallery_style;
    protected $gallery_default_settings;
    const SCREEN_PREVIEW_SIZE = [800, 260];

    public function toArray()
    {
        return [
            'items_id' => $this->gallery,
            'effect' => $this->gallery_style,
            'gap' => $this->gap(),
            'columns' => $this->columns(),
        ];
    }

    protected function gap()
    {
        switch ($this->gallery_style) {
            case 'default':
                return $this->gallery_default_settings['gap'] ?? '5';
            default:
                return null;
        }
    }

    protected function columns()
    {
        if (empty($this->gallery)) {
            return null;
        }

        switch ($this->gallery_style) {
            case 'default':
                return $this->gallery_default_settings['auto_size'] ? 'auto' : $this->gallery_default_settings['columns'];
            default:
                return null;
        }
    }
}
