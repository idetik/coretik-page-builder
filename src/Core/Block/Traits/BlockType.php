<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use Coretik\PageBuilder\Core\Block\Context\ContainerContext;
use Coretik\PageBuilder\Core\Contract\BlockContextInterface;

trait BlockType
{
    protected array $blockTypeProps = [];
    protected static BlockContextInterface $blockTypeContext;

    public static function bootBlockType(): void
    {
        static::$blockTypeContext = new ContainerContext(
            null,
            'wp-block-editor',
            null,
            null,
        );
    }

    public function registerBlockType(): array|bool
    {
        return \acf_register_block_type($this->getBlockType());
    }

    protected function prepareBlockTypeProps(array $customProps = []): array
    {
        $props = array_filter([
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

        return array_merge($props, $this->blockTypeProps, $customProps);
    }

    public function getBlockType(array $customProps = []): array
    {
        return $this->prepareBlockTypeProps($customProps);
    }

    public function getBlockTypeName(): string
    {
        return \str_replace('.', '-', $this->getName());
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
            if (array_key_exists('data', $attributes) && ($attributes['data']['is_preview'] ?? false)) {
                $data = $attributes['data'];

                if (!empty($data['preview_image'])) {
                    printf(
                        '<div class="block-preview-image block-preview-image--%s"><img style="width:100%%; height: auto;" src="%s" alt="Previewing %s" /></div>',
                        str_replace('.', '-', static::NAME),
                        $data['preview_image'],
                        static::LABEL,
                    );
                    return;
                }
            } else {
                $data = get_fields() ?: [];
            }

            $data['acf_fc_layout'] = static::NAME;

            $block = app()->get('pageBuilder.factory')->create($data);
            if (empty($data['uniqId'])) {
                $data['uniqId'] = $block->getUniqId();
            }

            $block->setContext(static::$blockTypeContext);

            $block->setProps($data);
            \do_action('coretik/page-builder/block/load', $block, $attributes);
            \do_action('coretik/page-builder/block/load/layoutId=' . $block->getLayoutId(), $block, $attributes);
            \do_action('coretik/page-builder/block/load/uniqId=' . $block->getUniqId(), $block, $attributes);
            $block->render();
        };
    }

    public function getBlockTypeEnqueueStyle(): ?string
    {
        return \app()->assets()->url('styles/admin.css', ASSETS_VERSIONING_STYLES);
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
                'data' => [
                    'is_preview' => true,
                    'preview_image' => apply_filters(
                        'coretik/block_type/preview_image',
                        str_replace('<##ASSETS_URL##>', app()->assets()->url(''), $this->thumbnail())
                    ),
                ],
            ],
        ];
    }
}
