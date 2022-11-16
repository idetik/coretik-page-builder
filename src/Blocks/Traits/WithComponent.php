<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

trait WithComponent
{
    protected function component(mixed $data)
    {
        return $this->config('factory')->create($data);
    }
}
