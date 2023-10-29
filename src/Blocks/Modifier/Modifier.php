<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


abstract class Modifier
{
    const NAME = '';
    const PRIORITY = 10;
    const SINGLETON = true;

    protected static $instances = [];
    protected mixed $args;

    abstract public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder;

    protected static function getInstance(): self
    {
        if (!array_key_exists(static::NAME, static::$instances)) {
            static::$instances[static::NAME] = static::make();
        }
        return static::$instances[static::NAME];
    }

    public static function make(mixed $args = null): self
    {
        $instance = new static();

        if (!\is_null($args)) {
            $instance->setArgs($args);
        }

        return $instance;
    }

    public static function modify(BlockInterface|string $block, mixed $args = null): BlockInterface
    {
        if (\is_string($block)) {
            $block = \app()->get('pageBuilder.factory')->create($block::NAME);
        }

        if (!empty($args) || !static::SINGLETON) {
            $instance = static::make($args);
        } else {
            $instance = static::getInstance();
        }

        $block->addModifier([$instance, 'handle'], static::PRIORITY);
        return $block;
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getArgs(): mixed
    {
        return $this->args;
    }
    
    /**
     * @param mixed $args 
     */
    public function setArgs(mixed $args): self
    {
        $this->args = $args;
        return $this;
    }
}
