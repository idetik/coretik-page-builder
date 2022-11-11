<?php

namespace Themetik\Services\PageBuilder\Blocks;

class Inserts extends Block
{
    use Traits\Flow;

    // @deprecated since 0.31.5
    use Traits\Background;
    use Traits\Container;

    const NAME = 'content.inserts';
    const LABEL = 'Contenu: Encarts';
    const SCREEN_PREVIEW_SIZE = [1200, 540];

    protected $inserts;
    protected $layout;

    protected function formatInsert()
    {
        return \array_map(function ($insert) {
            $insert['href'] = !empty($insert['anchor']) ? sprintf('%s#%s', $insert['href'] ?? '', $insert['anchor']) : ($insert['href'] ?? '');
            $insert['more'] = $insert['add_hidden_text'] ? $insert['more'] : null;
            $insert['cta'] = $insert['use_cta'] ? $insert['cta_label'] : null;
            return $insert;
        }, $this->inserts);
    }

    public function toArray()
    {
        return [
            'inserts' => $this->formatInsert(),
            'layout' => $this->layout ?? 'full'
        ];
    }
}
