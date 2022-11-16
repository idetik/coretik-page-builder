<?php

namespace Coretik\PageBuilder\Blocks\Components;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;
use Coretik\PageBuilder\Blocks\Traits\Flow;

use function Globalis\WP\Cubi\include_template_part;

abstract class Component extends Block
{
    const SCREENSHOTABLE = false;
    const IN_LIBRARY = false;
}
