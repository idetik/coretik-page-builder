<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Core\Contract\BlockInterface;
use Coretik\PageBuilder\Core\Block\ParentContext;
use SplObjectStorage;

trait Composite
{
    use Components;

    protected SplObjectStorage $children;

    protected function initializeComposite()
    {
        $this->children = new SplObjectStorage();
        $this->components = $this->prepareComponents();

        foreach ($this->components ?? [] as $key => $componentClass) {
            if (\is_int($key)) {
                $key = static::undot($componentClass::NAME);
            }

            $component = $this->compose($componentClass, $key);
            \add_filter('coretik/page-builder/fake-it/name=' . $this->getName(), fn ($props) => $props + [$key => $component->fakeIt()->getPropsFilled() + ['acf_fc_layout' => $component->getName()]]);
            $this->$key = null;
        }
    }

    protected function prepareComponents(): array
    {
        return $this->components;
    }

    public function compose(array|string|BlockInterface $block, ?string $key = null): BlockInterface
    {
        if (\is_string($block) || \is_array($block)) {
            // Handle block class name (block::class)
            if (\is_a($block, BlockInterface::class, true)) {
                $block = $block::NAME;
            }

            $block = $this->component($block);
        }

        $block->setContext(ParentContext::contextualize($this));

        if (!$this->children->contains($block)) {
            $this->children->attach($block, $key);
        }
        return $block;
    }

    protected static function undot(string $dotted): string
    {
        return \str_replace('.', '_dot_', $dotted);
    }

    protected static function dot(string $undotted): string
    {
        return \str_replace('_dot_', '.', $undotted);
    }

    public function fieldsComposite()
    {
        $fields = [];

        while ($this->children->valid()) {
            $block = $this->children->current();
            $blockFields = $block->fields();
            $blockFields->addField('acf_fc_layout', 'acfe_hidden', ['default_value' => $block->getName()]);

            $compositeField = new FieldsBuilder('');
            $compositeField
                ->addGroup($this->children->getInfo() ?? static::undot($block->getName()), ['acfe_seamless_style' => 1])
                    ->setLabel('')
                    ->addFields($blockFields)
                ->end();


            $fields[$this->children->getInfo() ?? $block->getName()] = [
                'fields' => $compositeField,
                'block' => $block
            ];
            $this->children->next();
        }

        return $fields;
    }

    protected function renderComponent(BlockInterface|string $key)
    {
        return $this->resolveComponent($this->$key)->render(true);
    }

    protected function componentToArray(BlockInterface|string $key): array
    {
        return $this->resolveComponent($this->$key)->toArray();
    }

    protected function resolveComponent(BlockInterface|string $key): BlockInterface
    {
        if ($key instanceof BlockInterface) {
            $key = static::undot($key::NAME);
        }

        if (empty($this->$key)) {
            return null;
        }

        return $this->compose($this->$key);
    }
}
