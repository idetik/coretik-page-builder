<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


abstract class Modifier
{
    const NAME = '';

    protected static $instances = [];

    abstract public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder;

    protected static function getInstance(): self
    {
        if (!array_key_exists(static::NAME, static::$instances)) {
            static::$instances[static::NAME] = new static();
        }
        return static::$instances[static::NAME];
    }

    public static function modify(BlockInterface|string $block): BlockInterface
    {
        if (\is_string($block)) {
            $block = \app()->get('pageBuilder.factory')->create($block::NAME);
        }

        $block->addModifier([static::getInstance(), 'handle']);
        return $block;
    }

    public function getName(): string
    {
        return static::NAME;
    }
}
