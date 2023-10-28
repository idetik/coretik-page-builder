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

        if (\method_exists($this, 'prepareComponents')) {
            $this->prepareComponents();
        }

        foreach ($this->components ?? [] as $key => $componentClass) {
            if (\is_int($key)) {
                $key = static::undot($componentClass::NAME);
            }
            $component = $this->compose($componentClass, $key);
            \add_filter('coretik/page-builder/fake-it/name=' . $this->getName(), fn ($props) => $props + [$key => $component->fakeIt()->getPropsFilled() + ['acf_fc_layout' => $component->getName()]]);
            $this->$key = null;
        }
    }

    public function compose(array|string|BlockInterface $block, ?string $key = null): BlockInterface
    {
        $context = [
            'block' => static::NAME,
            'type' => static::CATEGORY ?? explode('.', static::NAME)[0],
            'name' => $this->getName(),
        ];

        if (\is_string($block) || \is_array($block)) {

            // Handle block class name (block::class)
            if (\is_a($block, BlockInterface::class, true)) {
                $block = $block::NAME;
            }

            $block = $this->component($block);
        }

        $block->setContext($context);

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

    protected function renderComponent($key)
    {
        if ($key instanceof BlockInterface) {
            $key = static::undot($key::NAME);
        }

        if (empty($this->$key)) {
            return null;
        }

        return $this->compose($this->$key)->render(true);
    }

    protected function componentToArray($key): array
    {
        if ($key instanceof BlockInterface) {
            $key = static::undot($key::NAME);
        }

        if (empty($this->$key)) {
            return [];
        }

        return $this->compose($this->$key)->toArray();
    }
}
