<?php

namespace Themetik\Services\PageBuilder\Blocks;

class Services extends Block
{
    use Traits\Flow;
    use Traits\Background;

    const NAME = 'content.services';
    const LABEL = 'Contenu: Présentation des services';

    protected $services;
    protected $cta;

    // @deprecated since 0.31.5
    protected $title;

    public function toArray()
    {
        return [
            'title' => $this->title,
            'services' => $this->services,
            'cta' => $this->cta
        ];
    }
}
