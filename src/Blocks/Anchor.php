<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

class Anchor extends Block
{
    use Traits\Flow;

    const NAME = 'tools.anchor';
    const LABEL = 'Outils: Ancre';
    const SCREENSHOTABLE = false;

    protected $anchor;

    public function thumbnail(): string
    {
        return "data:image/jpeg;base64,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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD/9k=";
    }

    public function toArray()
    {
        return [
            'anchor' => $this->anchor,
        ];
    }
}
