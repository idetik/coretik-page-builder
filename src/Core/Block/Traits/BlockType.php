<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

trait BlockType
{
    public function registerBlockType(): void
    {
        \acf_register_block_type($this->getBlockType());
    }

    public function getBlockType(): array
    {
        return array_filter([
            'name' => $this->getBlockTypeName(),
            'title' => $this->getBlockTypeTitle(),
            'description' => $this->getBlockTypeDescription(),
            'category' => $this->getBlockTypeCategory(),
            'icon' => $this->getBlockTypeIcon(),
            'keywords' => $this->getBlockTypeKeywords(),
            'post_types' => $this->getBlockTypePostTypes(),
            'mode' => $this->getBlockTypeMode(),
            'align' => $this->getBlockTypeAlign(),
            'align_text' => $this->getBlockTypeAlignText(),
            'align_content' => $this->getBlockTypeAlignContent(),
            'render_template' => $this->getBlockTypeRenderTemplate(),
            'render_callback' => $this->getBlockTypeRender(),
            'enqueue_style' => $this->getBlockTypeEnqueueStyle(),
            'enqueue_script' => $this->getBlockTypeEnqueueScript(),
            'enqueue_assets' => $this->getBlockTypeEnqueueAssets(),
            'supports' => $this->getBlockTypeSupports(),
            'example' => $this->getBlockTypeExample(),
        ], fn ($value) => isset($value));
    }

    public function getBlockTypeName(): string
    {
        return 'acf/' . \str_replace('.', '-', $this->getName());
    }

    public function getBlockTypeTitle(): string
    {
        return $this->getLabel();
    }

    public function getBlockTypeDescription(): ?string
    {
        return null;
    }

    public function getBlockTypeIcon(): null|string|array
    {
        return null;
    }

    public function getBlockTypeKeywords(): ?array
    {
        return null;
    }

    public function getBlockTypePostTypes(): ?array
    {
        return null;
    }

    public function getBlockTypeCategory(): string
    {
        return $this->getCategory();
    }

    public function getBlockTypeMode(): ?string
    {
        return 'auto';
    }

    public function getBlockTypeAlign(): ?string
    {
        return 'full';
    }

    public function getBlockTypeAlignText(): ?string
    {
        return 'center';
    }

    public function getBlockTypeAlignContent():?string
    {
        return 'center';
    }

    public function getBlockTypeRenderTemplate(): string
    {
        return $this->template();
    }

    public function getBlockTypeRender(): ?callable
    {
        return function ($attributes, $content) {
            $data = get_fields();
            $data['acf_fc_layout'] = static::NAME;

            $block = app()->get('pageBuilder.factory')->create($data);
            if (empty($data['uniqId'])) {
                $data['uniqId'] = $block->getUniqId();
            }
            $block->setProps($data);
            \do_action('coretik/page-builder/block/load', $block, $attributes);
            \do_action('coretik/page-builder/block/load/layoutId=' . $block->getLayoutId(), $block, $attributes);
            \do_action('coretik/page-builder/block/load/uniqId=' . $block->getUniqId(), $block, $attributes);
            $block->render();
        };
    }

    public function getBlockTypeEnqueueStyle(): ?string
    {
        return null;
    }

    public function getBlockTypeEnqueueScript(): ?string
    {
        return null;
    }

    public function getBlockTypeEnqueueAssets(): ?callable
    {
        return null;
    }

    public function getBlockTypeSupports(): ?array
    {
        return null;
    }

    public function getBlockTypeExample(): ?array
    {
        return [
            'attributes' => [
                'mode' => 'preview',
                'data' => $this->fakeIt()->toArray(),
            ],
        ];
    }
}
