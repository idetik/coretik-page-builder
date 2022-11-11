<?php

namespace Themetik\Services\PageBuilder\Blocks;

class FeaturesPricing extends Block
{
    use Traits\Flow;
    use Traits\Container;

    const NAME = 'content.features-pricing';
    const LABEL = 'Contenu: Prestations & tarifs';
    const SCREEN_PREVIEW_SIZE = [1440, 651];

    protected $pricings;
    protected $layout;

    public function toArray()
    {
        return [
            'pricings' => $this->pricings,
            'layout' => $this->layout
        ];
    }
}
