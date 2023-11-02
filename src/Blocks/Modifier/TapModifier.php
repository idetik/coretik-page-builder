<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


class TapModifier extends Modifier
{
    const NAME = 'tap';
    const SINGLETON = false;

    protected $callback;

    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        return \call_user_func($this->callback, $fields, $block);
    }

    public function setArgs(mixed $callback): self
    {
        if (empty($callback) || !\is_callable($callback)) {
            throw new \Exception('[TapModifier] Callback is required.');
        }

        $this->callback = $callback;
        return $this;
    }
}
