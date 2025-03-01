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
    protected array $repeaterArgs = [];
    protected BlockInterface $repeaterFields;

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
            $instance->repeaterName = $args['name'] ?? null;
            $instance->repeaterArgs = $args['args'] ?? [];
        }

        if (!isset($instance->repeaterName)) {
            $instance->repeaterName = sprintf(
                '%s_rows',
                str_replace(['-', '.'], '_', $block->getName())
            );
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
