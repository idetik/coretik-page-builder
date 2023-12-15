<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use Coretik\PageBuilder\Library\Component\RepeatearComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;
use Exception;

class RepeatModifier extends Modifier
{
    const NAME = 'repeat';
    const PRIORITY = 100;

    protected string $repeaterName;
    protected array $repeaterArgs;
    protected BlockInterface $repeaterFields;

    public static function make(mixed $args = null): self
    {
        if (empty($args)) {
            throw new Exception('[RepeatModifier] String $repeaterName or array $repeaterArgs args are expected.');
        }

        if (\is_array($args)) {
            if (!\array_key_exists('name', $args)) {
                throw new Exception('[RepeatModifier] Repeater modifiers doesn\'t contains \'name\' or \'args\' keys.');
            }

            if (!\is_string($args['name'])) {
                throw new Exception('[RepeatModifier] Repeater modifiers \'name\' must be a string.');
            }

            if (!\is_array($args['args'] ?? [])) {
                throw new Exception('[RepeatModifier] Repeater modifiers \'args\' must be a array.');
            }
        } elseif (!\is_string($args)) {
            throw new Exception('[RepeatModifier] String $repeaterName or array $repeaterArgs args are expected.');
        }

        return parent::make($args);
    }

    public static function modify(BlockInterface|string $block, mixed $args = null): BlockInterface
    {
        if (\is_string($block)) {
            $block = \app()->get('pageBuilder.factory')->create($block::NAME);
        }

        $instance = static::make($args);

        $args = $instance->getArgs();

        if (\is_string($args)) {
            $instance->repeaterName = $args;
            $instance->repeaterArgs = [];
        }

        if (\is_array($args)) {
            $instance->repeaterName = $args['name'];
            $instance->repeaterArgs = $args['args'] ?? [];
        }

        $block->addModifier([$instance, 'handle'], static::PRIORITY);

        $repeaterFields = $block->config('factory')->create(RepeatearComponent::NAME)
            ->setRepeaterName($instance->repeaterName)
            ->setArgs($instance->repeaterArgs)
            ->setComponent($block->removeModifier($instance));

        $instance->setRepeaterFields($repeaterFields);

        return $repeaterFields;
    }

    public function setRepeaterFields(BlockInterface $repeaterFields): self
    {
        $this->repeaterFields = $repeaterFields;
        return $this;
    }

    /**
     * Return repeater fields
     */
    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        return $this->repeaterFields->fields();
    }
}
