<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\BlockInterface;

trait Composite
{
    use WithComponent;

    protected $children;

    protected function initializeComposite()
    {
        $this->children = new \SplObjectStorage();

        foreach ($this->components ?? [] as $componentClass) {
            $component = $this->compose($componentClass::NAME);
            $key = static::undot($component->getName());
            \add_filter('coretik/page-builder/fake-it/name=' . $this->getName(), fn ($props) => $props + [$key => $component->fakeIt()->getPropsFilled() + ['acf_fc_layout' => $component->getName()]]);
            $this->$key = null;
        }
    }

    public function compose(array|string|BlockInterface $block): BlockInterface
    {
        $context = [
            'block' => static::NAME,
            'type' => static::CATEGORY ?? explode('.', static::NAME)[0],
            'name' => explode('.', static::NAME)[1],
        ];

        if (\is_string($block) || \is_array($block)) {
            $block = $this->component($block);
        }

        $block->setContext($context);

        if (!$this->children->contains($block)) {
            $this->children->attach($block);
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
                ->addGroup(static::undot($block->getName()), ['acfe_seamless_style' => 1])
                    ->setLabel('')
                    ->addFields($blockFields)
                ->end();


            $fields[$block->getName()] = [
                'fields' => $compositeField,
                'block' => $block
            ];
            $this->children->next();
        }

        return $fields;
    }

    protected function renderComponent($class)
    {
        $key = static::undot($class::NAME);
        if (empty($this->$key)) {
            return null;
        }
        return $this->compose($this->$key)->render(true);
    }

    protected function componentToArray($class): array
    {
        $key = static::undot($class::NAME);
        if (empty($this->$key)) {
            return [];
        }
        return $this->compose($this->$key)->toArray();
    }
}
