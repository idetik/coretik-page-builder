<?php

namespace Coretik\PageBuilder\Core\Contract;

interface ShouldBuildBlockType
{
    public function registerBlockType(array $props = []): array|bool;
    public function getBlockType(array $customProps = []): array;
    public function getBlockTypeName(): string;
    public function getBlockTypeTitle(): string;
    public function getBlockTypeDescription(): ?string;
    public function getBlockTypeIcon(): null|string|array;
    public function getBlockTypeKeywords(): ?array;
    public function getBlockTypePostTypes(): ?array;
    public function getBlockTypeCategory(): string;
    public function getBlockTypeMode(): ?string;
    public function getBlockTypeAlign(): ?string;
    public function getBlockTypeAlignText(): ?string;
    public function getBlockTypeAlignContent(): ?string;
    public function getBlockTypeRenderTemplate(): string;
    public function getBlockTypeRender(): ?callable;
    public function getBlockTypeEnqueueStyle(): ?string;
    public function getBlockTypeEnqueueScript(): ?string;
    public function getBlockTypeEnqueueAssets(): ?callable;
    public function getBlockTypeSupports(): ?array;
    public function getBlockTypeExample(): ?array;
}